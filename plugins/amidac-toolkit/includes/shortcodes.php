<?php
/**
 * Shortcodes : [amidac_equipe], [amidac_partenaires], [amidac_rapports].
 * Utilisables dans Elementor via le widget « Shortcode ».
 */

defined( 'ABSPATH' ) || exit;

/**
 * Requête commune : tri manuel (ordre de page) puis titre.
 */
function amidac_toolkit_query( $post_type, $limit, $extra = array() ) {
    return new WP_Query( array_merge( array(
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
        'no_found_rows'  => true,
    ), $extra ) );
}

/**
 * [amidac_equipe departement="direction" limit="12"]
 */
function amidac_toolkit_sc_equipe( $atts ) {
    $a = shortcode_atts( array( 'departement' => '', 'limit' => 50 ), $atts, 'amidac_equipe' );

    $extra = array();
    if ( $a['departement'] ) {
        $extra['tax_query'] = array( array(
            'taxonomy' => 'departement',
            'field'    => 'slug',
            'terms'    => sanitize_title( $a['departement'] ),
        ) );
    }

    $q = amidac_toolkit_query( 'amidac_membre', absint( $a['limit'] ), $extra );
    if ( ! $q->have_posts() ) {
        return '';
    }

    $out = '<div class="amidac-team">';
    while ( $q->have_posts() ) {
        $q->the_post();
        $poste = get_post_meta( get_the_ID(), '_amidac_poste', true );
        $out  .= '<div class="amidac-member">';
        $out  .= get_the_post_thumbnail( get_the_ID(), 'medium' );
        $out  .= '<h3>' . esc_html( get_the_title() ) . '</h3>';
        if ( $poste ) {
            $out .= '<small>' . esc_html( $poste ) . '</small>';
        }
        $out .= '</div>';
    }
    wp_reset_postdata();

    return $out . '</div>';
}
add_shortcode( 'amidac_equipe', 'amidac_toolkit_sc_equipe' );

/**
 * [amidac_partenaires limit="12"]
 */
function amidac_toolkit_sc_partenaires( $atts ) {
    $a = shortcode_atts( array( 'limit' => 50 ), $atts, 'amidac_partenaires' );

    $q = amidac_toolkit_query( 'amidac_partenaire', absint( $a['limit'] ) );
    if ( ! $q->have_posts() ) {
        return '';
    }

    $out = '<div class="amidac-partners">';
    while ( $q->have_posts() ) {
        $q->the_post();
        $logo = get_the_post_thumbnail( get_the_ID(), 'medium', array( 'alt' => get_the_title() ) );
        $url  = get_post_meta( get_the_ID(), '_amidac_site_web', true );
        if ( ! $logo ) {
            $logo = esc_html( get_the_title() );
        }
        $out .= $url
            ? '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener">' . $logo . '</a>'
            : '<span>' . $logo . '</span>';
    }
    wp_reset_postdata();

    return $out . '</div>';
}
add_shortcode( 'amidac_partenaires', 'amidac_toolkit_sc_partenaires' );

/**
 * Documents publiés d'une catégorie. Les anciens rapports sans catégorie comptent comme annuels.
 */
function amidac_toolkit_documents( $categorie, $annee = 0, $limit = 100 ) {
    $meta = array( array( 'key' => '_amidac_categorie', 'value' => $categorie ) );
    if ( 'annuel' === $categorie ) {
        $meta = array( 'relation' => 'OR', $meta[0], array( 'key' => '_amidac_categorie', 'compare' => 'NOT EXISTS' ) );
    }
    if ( $annee ) {
        $meta = array( 'relation' => 'AND', array( 'key' => '_amidac_annee', 'value' => $annee ), $meta );
    }

    $docs = get_posts( array(
        'post_type'      => 'amidac_rapport',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
        'meta_query'     => $meta,
    ) );

    if ( 'annuel' === $categorie ) {
        usort( $docs, function ( $a, $b ) {
            $ya = (int) get_post_meta( $a->ID, '_amidac_annee', true );
            $yb = (int) get_post_meta( $b->ID, '_amidac_annee', true );
            return $ya !== $yb ? $yb <=> $ya : $a->menu_order <=> $b->menu_order;
        } );
    }
    return $docs;
}

/**
 * Attributs lus par le script de recherche et de filtre (assets/js/rapports-front.js).
 */
function amidac_toolkit_document_data( $doc ) {
    $texte = $doc->post_title . ' ' . $doc->post_excerpt . ' ' . get_post_meta( $doc->ID, '_amidac_groupe', true )
        . ' ' . get_post_meta( $doc->ID, '_amidac_type_rapport', true );

    return sprintf(
        ' data-annee="%s" data-texte="%s"',
        esc_attr( get_post_meta( $doc->ID, '_amidac_annee', true ) ),
        esc_attr( $texte )
    );
}

/**
 * Boutons de filtre par catégorie à gauche, recherche à droite, sur une seule ligne.
 */
function amidac_toolkit_documents_filtres( $cats ) {
    $td = 'amidac-toolkit';

    $groupe = function ( $nom, $label, $choix, $tous ) {
        $out = '<div class="amidac-filtres__groupe" role="group" data-filtre="' . esc_attr( $nom ) . '" aria-label="' . esc_attr( $label ) . '">';
        $out .= '<button type="button" data-valeur="" aria-pressed="true">' . esc_html( $tous ) . '</button>';
        foreach ( $choix as $valeur => $texte ) {
            $out .= '<button type="button" data-valeur="' . esc_attr( $valeur ) . '" aria-pressed="false">' . esc_html( $texte ) . '</button>';
        }
        return $out . '</div>';
    };

    $out = '<div class="amidac-filtres">';
    if ( count( $cats ) > 1 ) {
        // Libellés courts pour que les boutons et la recherche tiennent sur une ligne.
        $courts = array(
            'annuel'         => __( 'Rapports', $td ),
            'publication'    => __( 'Publications', $td ),
            'reseau'         => __( 'Réseaux', $td ),
            'institutionnel' => __( 'Institutionnel', $td ),
        );
        foreach ( $cats as $cle => $libelle ) {
            $cats[ $cle ] = isset( $courts[ $cle ] ) ? $courts[ $cle ] : $libelle;
        }
        $out .= $groupe( 'categorie', __( 'Catégorie', $td ), $cats, __( 'Tous', $td ) );
    }

    $out .= '<label class="amidac-filtres__champ">'
        . '<svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true"><circle cx="8.5" cy="8.5" r="5.5" fill="none" stroke="currentColor" stroke-width="2"/><path d="M13 13l5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>'
        . '<input type="search" class="amidac-filtres__recherche" placeholder="' . esc_attr__( 'Rechercher un document…', $td ) . '" aria-label="' . esc_attr__( 'Rechercher un document', $td ) . '">'
        . '</label>';

    return $out . '</div><p class="amidac-filtres__vide" hidden>' . esc_html__( 'Aucun document ne correspond à votre recherche.', $td ) . '</p>';
}

function amidac_toolkit_document_bouton( $post_id ) {
    $file = get_post_meta( $post_id, '_amidac_fichier_url', true );
    if ( ! $file ) {
        return '<span class="amidac-doc__btn amidac-doc__btn--off">' . esc_html__( 'Bientôt disponible', 'amidac-toolkit' ) . '</span>';
    }
    return '<a class="amidac-doc__btn" href="' . esc_url( $file ) . '" target="_blank" rel="noopener">'
        . esc_html__( 'Télécharger', 'amidac-toolkit' ) . '</a>';
}

/**
 * Carte d'un document : année, titre, description, date et bouton.
 */
function amidac_toolkit_document_carte( $doc ) {
    $annee   = get_post_meta( $doc->ID, '_amidac_annee', true );
    $periode = get_post_meta( $doc->ID, '_amidac_periode', true );

    $out = '<article class="amidac-doc"' . amidac_toolkit_document_data( $doc ) . '>';
    if ( $annee ) {
        $out .= '<span class="amidac-pill">' . esc_html( $annee ) . '</span>';
    }
    $out .= '<h3 class="amidac-doc__titre">' . esc_html( get_the_title( $doc ) ) . '</h3>';
    if ( $doc->post_excerpt ) {
        $out .= '<p class="amidac-doc__desc">' . esc_html( $doc->post_excerpt ) . '</p>';
    }
    $out .= '<div class="amidac-doc__pied">';
    $out .= '<span class="amidac-doc__date">' . esc_html( $periode ) . '</span>';
    $out .= amidac_toolkit_document_bouton( $doc->ID );
    $out .= '</div></article>';

    return $out;
}

/**
 * Documents institutionnels : un panneau par groupe, une ligne par document.
 */
function amidac_toolkit_documents_groupes( $docs ) {
    $groupes = array();
    foreach ( $docs as $doc ) {
        $nom = get_post_meta( $doc->ID, '_amidac_groupe', true );
        $groupes[ $nom ? $nom : __( 'Documents', 'amidac-toolkit' ) ][] = $doc;
    }

    $out = '<div class="amidac-docs__groupes">';
    foreach ( $groupes as $nom => $liste ) {
        $out .= '<div class="amidac-docs__groupe"><h3>' . esc_html( $nom ) . '</h3><ul>';
        foreach ( $liste as $doc ) {
            $file  = get_post_meta( $doc->ID, '_amidac_fichier_url', true );
            $titre = esc_html( get_the_title( $doc ) );
            $out  .= '<li' . amidac_toolkit_document_data( $doc ) . '>' . ( $file
                ? '<a href="' . esc_url( $file ) . '" target="_blank" rel="noopener">' . $titre . '</a>'
                : '<span class="amidac-docs__off">' . $titre . ' <small>' . esc_html__( 'Bientôt disponible', 'amidac-toolkit' ) . '</small></span>' ) . '</li>';
        }
        $out .= '</ul></div>';
    }
    return $out . '</div>';
}

/**
 * Documents nombreux (comptes rendus, missions) : un repli par groupe, une ligne par document.
 * Les groupes sont classés par année la plus récente ; tous sont fermés au départ.
 */
function amidac_toolkit_documents_replis( $docs ) {
    $groupes = array();
    foreach ( $docs as $doc ) {
        $nom = get_post_meta( $doc->ID, '_amidac_groupe', true );
        $groupes[ $nom ? $nom : __( 'Autres documents', 'amidac-toolkit' ) ][] = $doc;
    }

    $annee_max = function ( $liste ) {
        return max( array_map( function ( $d ) {
            return (int) get_post_meta( $d->ID, '_amidac_annee', true );
        }, $liste ) );
    };
    uasort( $groupes, function ( $a, $b ) use ( $annee_max ) {
        return $annee_max( $b ) <=> $annee_max( $a );
    } );

    $out = '<div class="amidac-replis">';
    foreach ( $groupes as $nom => $liste ) {
        // Les documents datés se lisent du plus récent au plus ancien.
        if ( $annee_max( $liste ) && ! array_filter( $liste, function ( $d ) {
            return ! get_post_meta( $d->ID, '_amidac_annee', true );
        } ) ) {
            $liste = array_reverse( $liste );
        }

        $out .= '<details class="amidac-repli" data-ouvert="0">';
        $out .= '<summary><span class="amidac-repli__nom">' . esc_html( $nom ) . '</span><span class="amidac-repli__nb">' . count( $liste ) . '</span></summary><ul class="amidac-repli__liste">';
        foreach ( $liste as $doc ) {
            $out .= '<li' . amidac_toolkit_document_data( $doc ) . '>'
                . '<span class="amidac-repli__titre">' . esc_html( get_the_title( $doc ) ) . '</span>'
                . '<span class="amidac-doc__date">' . esc_html( get_post_meta( $doc->ID, '_amidac_periode', true ) ) . '</span>'
                . amidac_toolkit_document_bouton( $doc->ID ) . '</li>';
        }
        $out .= '</ul></details>';
    }

    return $out . '</div>';
}

/**
 * [amidac_rapports categorie="annuel|publication|reseau|institutionnel" annee="2024" limit="50" titres="oui"]
 * Sans catégorie : toutes les sections, avec leur titre et la barre de recherche, dans l'ordre de la page Rapports & Publications.
 * filtres="oui|non" force ou masque la barre de recherche.
 */
function amidac_toolkit_sc_rapports( $atts ) {
    $a = shortcode_atts( array( 'categorie' => '', 'annee' => '', 'limit' => 100, 'titres' => '', 'filtres' => '' ), $atts, 'amidac_rapports' );

    $cats = amidac_toolkit_rapport_categories();
    if ( isset( $cats[ $a['categorie'] ] ) ) {
        $cats   = array( $a['categorie'] => $cats[ $a['categorie'] ] );
        $titres  = in_array( $a['titres'], array( 'oui', '1', 'true' ), true );
        $filtres = in_array( $a['filtres'], array( 'oui', '1', 'true' ), true );
    } else {
        $titres  = 'non' !== $a['titres'];
        $filtres = 'non' !== $a['filtres'];
    }

    $out = '';
    foreach ( $cats as $key => $label ) {
        $docs = amidac_toolkit_documents( $key, absint( $a['annee'] ), absint( $a['limit'] ) );
        if ( ! $docs ) {
            continue;
        }

        $out .= '<section class="amidac-docs amidac-docs--' . esc_attr( $key ) . '" data-categorie="' . esc_attr( $key ) . '">';
        if ( $titres ) {
            $out .= '<h2 class="amidac-docs__titre">' . esc_html( $label ) . '</h2>';
        }
        if ( 'institutionnel' === $key ) {
            $out .= amidac_toolkit_documents_groupes( $docs );
        } elseif ( 'reseau' === $key ) {
            $out .= amidac_toolkit_documents_replis( $docs );
        } else {
            $out .= '<div class="amidac-docs__grille">';
            foreach ( $docs as $doc ) {
                $out .= amidac_toolkit_document_carte( $doc );
            }
            $out .= '</div>';
        }
        $out .= '</section>';
    }

    if ( ! $filtres || ! $out ) {
        return $out;
    }

    $js = AMIDAC_TOOLKIT_DIR . 'assets/js/rapports-front.js';
    wp_enqueue_script( 'amidac-rapports-front', AMIDAC_TOOLKIT_URI . 'assets/js/rapports-front.js', array(), AMIDAC_TOOLKIT_VERSION . '.' . filemtime( $js ), true );

    return '<div class="amidac-rapports-bloc">' . amidac_toolkit_documents_filtres( $cats ) . $out . '</div>';
}
add_shortcode( 'amidac_rapports', 'amidac_toolkit_sc_rapports' );
