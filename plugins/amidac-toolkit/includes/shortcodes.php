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
 * Barre de recherche, filtre par catégorie et filtre par année.
 */
function amidac_toolkit_documents_filtres( $cats, $annees ) {
    $td  = 'amidac-toolkit';
    $out = '<div class="amidac-filtres">';
    $out .= '<input type="search" class="amidac-filtres__recherche" placeholder="' . esc_attr__( 'Rechercher un document…', $td ) . '" aria-label="' . esc_attr__( 'Rechercher un document', $td ) . '">';

    if ( count( $cats ) > 1 ) {
        $out .= '<select class="amidac-filtres__categorie" aria-label="' . esc_attr__( 'Catégorie', $td ) . '"><option value="">' . esc_html__( 'Toutes les catégories', $td ) . '</option>';
        foreach ( $cats as $key => $label ) {
            $out .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
        }
        $out .= '</select>';
    }

    if ( $annees ) {
        rsort( $annees );
        $out .= '<select class="amidac-filtres__annee" aria-label="' . esc_attr__( 'Année', $td ) . '"><option value="">' . esc_html__( 'Toutes les années', $td ) . '</option>';
        foreach ( $annees as $annee ) {
            $out .= '<option value="' . esc_attr( $annee ) . '">' . esc_html( $annee ) . '</option>';
        }
        $out .= '</select>';
    }

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

    $out    = '';
    $annees = array();
    foreach ( $cats as $key => $label ) {
        $docs = amidac_toolkit_documents( $key, absint( $a['annee'] ), absint( $a['limit'] ) );
        if ( ! $docs ) {
            continue;
        }

        foreach ( $docs as $doc ) {
            $annee = get_post_meta( $doc->ID, '_amidac_annee', true );
            if ( $annee ) {
                $annees[ $annee ] = (int) $annee;
            }
        }

        $out .= '<section class="amidac-docs amidac-docs--' . esc_attr( $key ) . '" data-categorie="' . esc_attr( $key ) . '">';
        if ( $titres ) {
            $out .= '<h2 class="amidac-docs__titre">' . esc_html( $label ) . '</h2>';
        }
        if ( 'institutionnel' === $key ) {
            $out .= amidac_toolkit_documents_groupes( $docs );
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

    return '<div class="amidac-rapports-bloc">' . amidac_toolkit_documents_filtres( $cats, array_values( $annees ) ) . $out . '</div>';
}
add_shortcode( 'amidac_rapports', 'amidac_toolkit_sc_rapports' );
