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
 * [amidac_rapports annee="2025" limit="12"]
 */
function amidac_toolkit_sc_rapports( $atts ) {
    $a = shortcode_atts( array( 'annee' => '', 'limit' => 20 ), $atts, 'amidac_rapports' );

    $extra = array( 'orderby' => array( 'date' => 'DESC' ) );
    if ( $a['annee'] ) {
        $extra['meta_query'] = array( array(
            'key'   => '_amidac_annee',
            'value' => absint( $a['annee'] ),
        ) );
    }

    $q = amidac_toolkit_query( 'amidac_rapport', absint( $a['limit'] ), $extra );
    if ( ! $q->have_posts() ) {
        return '';
    }

    $out = '<div class="amidac-reports">';
    while ( $q->have_posts() ) {
        $q->the_post();
        $id    = get_the_ID();
        $file  = get_post_meta( $id, '_amidac_fichier_url', true );
        $annee = get_post_meta( $id, '_amidac_annee', true );
        $type  = get_post_meta( $id, '_amidac_type_rapport', true );

        $out .= '<div class="amidac-report">';
        $out .= '<small>' . esc_html( trim( $type . ' ' . $annee ) ) . '</small>';
        $out .= '<h3>' . esc_html( get_the_title() ) . '</h3>';
        if ( $file ) {
            $out .= '<a class="amidac-btn" href="' . esc_url( $file ) . '" target="_blank" rel="noopener">'
                . esc_html__( 'Télécharger', 'amidac-toolkit' ) . '</a>';
        }
        $out .= '</div>';
    }
    wp_reset_postdata();

    return $out . '</div>';
}
add_shortcode( 'amidac_rapports', 'amidac_toolkit_sc_rapports' );
