<?php
/**
 * AMIDAC — Shortcodes personnalisés
 *
 * @package AMIDAC_Kadence_Child
 */

defined( 'ABSPATH' ) || exit;

// ============================================================================
// [amidac_phone] — Affiche le numéro de téléphone
// ============================================================================
add_shortcode( 'amidac_phone', function( $atts ) {
    $atts = shortcode_atts( array( 'link' => 'yes' ), $atts );
    $phone = get_theme_mod( 'amidac_phone', '' );
    if ( ! $phone ) return '';
    if ( 'yes' === $atts['link'] ) {
        return '<a href="tel:' . esc_attr( preg_replace( '/\s+/', '', $phone ) ) . '" class="amidac-phone-link">' . esc_html( $phone ) . '</a>';
    }
    return '<span class="amidac-phone">' . esc_html( $phone ) . '</span>';
} );

// ============================================================================
// [amidac_email] — Affiche l'email de contact
// ============================================================================
add_shortcode( 'amidac_email', function( $atts ) {
    $atts  = shortcode_atts( array( 'link' => 'yes' ), $atts );
    $email = get_theme_mod( 'amidac_email', '' );
    if ( ! $email ) return '';
    if ( 'yes' === $atts['link'] ) {
        return '<a href="mailto:' . esc_attr( $email ) . '" class="amidac-email-link">' . esc_html( $email ) . '</a>';
    }
    return '<span class="amidac-email">' . esc_html( $email ) . '</span>';
} );

// ============================================================================
// [amidac_address] — Affiche l'adresse
// ============================================================================
add_shortcode( 'amidac_address', function() {
    $address = amidac_address();
    return '<address class="amidac-address">' . esc_html( $address ) . '</address>';
} );

// ============================================================================
// [amidac_cta] — Bouton Call-to-Action
// ============================================================================
add_shortcode( 'amidac_cta', function( $atts ) {
    $atts = shortcode_atts( array(
        'text'   => get_theme_mod( 'amidac_cta_text', __( 'Faire un don', 'amidac-kadence-child' ) ),
        'url'    => get_theme_mod( 'amidac_cta_url', '#don' ),
        'class'  => 'amidac-btn-accent',
        'target' => '_self',
    ), $atts );

    return '<a href="' . esc_url( $atts['url'] ) . '" class="amidac-btn ' . esc_attr( $atts['class'] ) . '" target="' . esc_attr( $atts['target'] ) . '">' . esc_html( $atts['text'] ) . '</a>';
} );

// ============================================================================
// [amidac_social_icons] — Icônes réseaux sociaux
// ============================================================================
add_shortcode( 'amidac_social_icons', function( $atts ) {
    $atts = shortcode_atts( array( 'class' => '' ), $atts );
    ob_start();
    amidac_social_icons( $atts['class'] );
    return ob_get_clean();
} );

// ============================================================================
// [amidac_year] — Année courante
// ============================================================================
add_shortcode( 'amidac_year', function() {
    return date( 'Y' );
} );

// ============================================================================
// [amidac_org_name] — Nom de l'organisation
// ============================================================================
add_shortcode( 'amidac_org_name', function() {
    return esc_html( amidac_org_name() );
} );

// ============================================================================
// [amidac_stat] — Statistique mise en valeur
// ============================================================================
add_shortcode( 'amidac_stat', function( $atts ) {
    $atts = shortcode_atts( array(
        'number' => '0',
        'label'  => '',
        'suffix' => '',
        'color'  => 'primary',
    ), $atts );

    return '<div class="amidac-stat amidac-stat--' . esc_attr( $atts['color'] ) . '">'
         . '<span class="amidac-stat__number">' . esc_html( $atts['number'] ) . esc_html( $atts['suffix'] ) . '</span>'
         . ( $atts['label'] ? '<span class="amidac-stat__label">' . esc_html( $atts['label'] ) . '</span>' : '' )
         . '</div>';
} );

// ============================================================================
// [amidac_alert] — Boîte d'alerte / info
// ============================================================================
add_shortcode( 'amidac_alert', function( $atts, $content = '' ) {
    $atts = shortcode_atts( array(
        'type'  => 'info',   // info | success | warning | danger
        'title' => '',
    ), $atts );

    $icons = array(
        'info'    => 'ℹ️',
        'success' => '✅',
        'warning' => '⚠️',
        'danger'  => '❌',
    );

    $icon = $icons[ $atts['type'] ] ?? 'ℹ️';

    $html  = '<div class="amidac-alert amidac-alert--' . esc_attr( $atts['type'] ) . '">';
    $html .= '<span class="amidac-alert__icon" aria-hidden="true">' . $icon . '</span>';
    $html .= '<div class="amidac-alert__body">';
    if ( $atts['title'] ) {
        $html .= '<strong class="amidac-alert__title">' . esc_html( $atts['title'] ) . '</strong>';
    }
    $html .= '<p>' . wp_kses_post( $content ) . '</p>';
    $html .= '</div></div>';

    return $html;
}, 10, 2 );

// ============================================================================
// [amidac_breadcrumbs] — Fil d'ariane
// ============================================================================
add_shortcode( 'amidac_breadcrumbs', function() {
    ob_start();
    amidac_breadcrumbs();
    return ob_get_clean();
} );
