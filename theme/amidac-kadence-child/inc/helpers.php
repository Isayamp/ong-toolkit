<?php
/**
 * AMIDAC — Fonctions utilitaires (helpers)
 *
 * @package AMIDAC_Kadence_Child
 */

defined( 'ABSPATH' ) || exit;

// ============================================================================
// INFORMATIONS DE L'ORGANISATION
// ============================================================================

/** Retourne le nom de l'organisation */
function amidac_org_name() {
    return get_theme_mod( 'amidac_org_name', 'AMIDAC asbl' );
}

/** Retourne l'email de contact */
function amidac_email() {
    return get_theme_mod( 'amidac_email', '' );
}

/** Retourne le téléphone */
function amidac_phone() {
    return get_theme_mod( 'amidac_phone', '' );
}

/** Retourne l'adresse complète formatée */
function amidac_address() {
    $parts = array_filter( array(
        get_theme_mod( 'amidac_address', '' ),
        get_theme_mod( 'amidac_city',    'Kinshasa' ),
        get_theme_mod( 'amidac_country', 'RDC' ),
    ) );
    return implode( ', ', $parts );
}

// ============================================================================
// RÉSEAUX SOCIAUX
// ============================================================================

/**
 * Retourne les liens des réseaux sociaux configurés.
 * @return array
 */
function amidac_get_social_links() {
    $networks = array(
        'facebook'  => array( 'label' => 'Facebook',   'icon' => 'fab fa-facebook-f' ),
        'twitter'   => array( 'label' => 'Twitter/X',  'icon' => 'fab fa-x-twitter' ),
        'instagram' => array( 'label' => 'Instagram',  'icon' => 'fab fa-instagram' ),
        'linkedin'  => array( 'label' => 'LinkedIn',   'icon' => 'fab fa-linkedin-in' ),
        'youtube'   => array( 'label' => 'YouTube',    'icon' => 'fab fa-youtube' ),
        'tiktok'    => array( 'label' => 'TikTok',     'icon' => 'fab fa-tiktok' ),
    );

    $links = array();
    foreach ( $networks as $key => $data ) {
        $url = get_theme_mod( 'amidac_' . $key, '' );
        if ( $url ) {
            $links[ $key ] = array_merge( $data, array( 'url' => esc_url( $url ) ) );
        }
    }
    return $links;
}

/**
 * Affiche les icônes des réseaux sociaux (HTML).
 * @param string $class CSS supplémentaire
 */
function amidac_social_icons( $class = '' ) {
    $links = amidac_get_social_links();
    if ( empty( $links ) ) return;

    echo '<ul class="amidac-social-links ' . esc_attr( $class ) . '">';
    foreach ( $links as $network => $data ) {
        echo '<li class="social-' . esc_attr( $network ) . '">';
        echo '<a href="' . esc_url( $data['url'] ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $data['label'] ) . '">';
        echo '<i class="' . esc_attr( $data['icon'] ) . '"></i>';
        echo '</a>';
        echo '</li>';
    }
    echo '</ul>';
}

// ============================================================================
// THUMBNAILS & IMAGES
// ============================================================================

/**
 * Thumbnail avec fallback sur une image placeholder.
 * @param int    $post_id
 * @param string $size
 * @param string $class
 */
function amidac_post_thumbnail( $post_id = null, $size = 'amidac-card', $class = '' ) {
    $post_id = $post_id ?: get_the_ID();

    if ( has_post_thumbnail( $post_id ) ) {
        echo get_the_post_thumbnail( $post_id, $size, array(
            'class'   => 'amidac-thumbnail ' . esc_attr( $class ),
            'loading' => 'lazy',
        ) );
    } else {
        $title = get_the_title( $post_id );
        echo '<div class="amidac-thumbnail-placeholder ' . esc_attr( $class ) . '" aria-label="' . esc_attr( $title ) . '">';
        echo '<span>' . esc_html( mb_substr( $title, 0, 2 ) ) . '</span>';
        echo '</div>';
    }
}

// ============================================================================
// EXCERPTS & CONTENU
// ============================================================================

/**
 * Excerpt personnalisé avec longueur contrôlée.
 * @param int    $length
 * @param string $more
 */
function amidac_excerpt( $length = 25, $more = '...' ) {
    echo wp_trim_words( get_the_excerpt(), $length, $more );
}

// ============================================================================
// FORMATAGE DES DATES
// ============================================================================

/** Retourne la date formatée en français */
function amidac_date( $format = null ) {
    if ( ! $format ) {
        $format = get_option( 'date_format' );
    }
    return get_the_date( $format );
}

// ============================================================================
// CTA
// ============================================================================

/**
 * Bouton CTA global configuré dans le Customizer.
 * @param string $extra_class
 */
function amidac_cta_button( $extra_class = '' ) {
    $text = get_theme_mod( 'amidac_cta_text', __( 'Faire un don', 'amidac-kadence-child' ) );
    $url  = get_theme_mod( 'amidac_cta_url',  '#don' );

    if ( $text && $url ) {
        echo '<a href="' . esc_url( $url ) . '" class="amidac-btn amidac-btn-accent ' . esc_attr( $extra_class ) . '">';
        echo esc_html( $text );
        echo '</a>';
    }
}

// ============================================================================
// BREADCRUMBS (FIL D'ARIANE)
// ============================================================================

/**
 * Fil d'ariane simple.
 */
function amidac_breadcrumbs() {
    if ( is_front_page() ) return;

    $separator = '<span class="amidac-breadcrumb-sep" aria-hidden="true">/</span>';
    $home_text = __( 'Accueil', 'amidac-kadence-child' );

    echo '<nav class="amidac-breadcrumbs" aria-label="' . esc_attr__( 'Fil d\'ariane', 'amidac-kadence-child' ) . '">';
    echo '<ol class="breadcrumb-list">';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( $home_text ) . '</a></li>';

    if ( is_category() || is_single() ) {
        echo '<li>' . $separator . '</li>';
        if ( is_single() ) {
            $cats = get_the_category();
            if ( $cats ) {
                echo '<li><a href="' . esc_url( get_category_link( $cats[0]->term_id ) ) . '">' . esc_html( $cats[0]->name ) . '</a></li>';
                echo '<li>' . $separator . '</li>';
            }
            echo '<li aria-current="page">' . esc_html( get_the_title() ) . '</li>';
        } else {
            echo '<li aria-current="page">' . esc_html( single_cat_title( '', false ) ) . '</li>';
        }
    } elseif ( is_page() ) {
        $ancestors = get_post_ancestors( get_the_ID() );
        if ( $ancestors ) {
            $ancestors = array_reverse( $ancestors );
            foreach ( $ancestors as $ancestor ) {
                echo '<li>' . $separator . '</li>';
                echo '<li><a href="' . esc_url( get_permalink( $ancestor ) ) . '">' . esc_html( get_the_title( $ancestor ) ) . '</a></li>';
            }
        }
        echo '<li>' . $separator . '</li>';
        echo '<li aria-current="page">' . esc_html( get_the_title() ) . '</li>';
    } elseif ( is_search() ) {
        echo '<li>' . $separator . '</li>';
        echo '<li aria-current="page">' . sprintf( esc_html__( 'Résultats pour : %s', 'amidac-kadence-child' ), '<q>' . esc_html( get_search_query() ) . '</q>' ) . '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}

// ============================================================================
// DEBUG (DÉSACTIVER EN PRODUCTION)
// ============================================================================

/**
 * Affiche une variable de débogage si WP_DEBUG est actif.
 * @param mixed $var
 * @param bool  $die
 */
function amidac_debug( $var, $die = false ) {
    if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) return;
    echo '<pre style="background:#1a1a2e;color:#f5a623;padding:1rem;border-radius:8px;overflow:auto;">';
    echo esc_html( print_r( $var, true ) );
    echo '</pre>';
    if ( $die ) die();
}
