<?php
/**
 * AMIDAC — Customizer
 * Paramètres du Personnaliseur WordPress
 *
 * @package AMIDAC_Kadence_Child
 */

defined( 'ABSPATH' ) || exit;

function amidac_customizer_settings( $wp_customize ) {

    // ========================================================================
    // PANEL PRINCIPAL AMIDAC
    // ========================================================================

    $wp_customize->add_panel( 'amidac_panel', array(
        'title'       => __( '🏢 AMIDAC asbl', 'amidac-kadence-child' ),
        'description' => __( 'Paramètres personnalisés pour le site AMIDAC asbl.', 'amidac-kadence-child' ),
        'priority'    => 30,
    ) );

    // ========================================================================
    // SECTION : INFORMATIONS DE L'ORGANISATION
    // ========================================================================

    $wp_customize->add_section( 'amidac_org_info', array(
        'title'    => __( 'Informations Organisation', 'amidac-kadence-child' ),
        'panel'    => 'amidac_panel',
        'priority' => 10,
    ) );

    // Nom de l'organisation
    $wp_customize->add_setting( 'amidac_org_name', array(
        'default'           => 'AMIDAC asbl',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'amidac_org_name', array(
        'label'   => __( "Nom de l'organisation", 'amidac-kadence-child' ),
        'section' => 'amidac_org_info',
        'type'    => 'text',
    ) );

    // Slogan / Mission
    $wp_customize->add_setting( 'amidac_org_mission', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'amidac_org_mission', array(
        'label'   => __( 'Mission / Slogan', 'amidac-kadence-child' ),
        'section' => 'amidac_org_info',
        'type'    => 'textarea',
    ) );

    // Numéro d'enregistrement ASBL
    $wp_customize->add_setting( 'amidac_reg_number', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'amidac_reg_number', array(
        'label'   => __( "Numéro d'enregistrement", 'amidac-kadence-child' ),
        'section' => 'amidac_org_info',
        'type'    => 'text',
    ) );

    // ========================================================================
    // SECTION : COORDONNÉES
    // ========================================================================

    $wp_customize->add_section( 'amidac_contact', array(
        'title'    => __( 'Coordonnées', 'amidac-kadence-child' ),
        'panel'    => 'amidac_panel',
        'priority' => 20,
    ) );

    $contact_fields = array(
        'amidac_phone'    => array( 'label' => __( 'Téléphone', 'amidac-kadence-child' ),          'default' => '', 'cb' => 'sanitize_text_field' ),
        'amidac_phone2'   => array( 'label' => __( 'Téléphone 2', 'amidac-kadence-child' ),        'default' => '', 'cb' => 'sanitize_text_field' ),
        'amidac_email'    => array( 'label' => __( 'Email de contact', 'amidac-kadence-child' ),    'default' => '', 'cb' => 'sanitize_email' ),
        'amidac_address'  => array( 'label' => __( 'Adresse', 'amidac-kadence-child' ),            'default' => '', 'cb' => 'sanitize_textarea_field', 'type' => 'textarea' ),
        'amidac_city'     => array( 'label' => __( 'Ville', 'amidac-kadence-child' ),              'default' => 'Kinshasa', 'cb' => 'sanitize_text_field' ),
        'amidac_country'  => array( 'label' => __( 'Pays', 'amidac-kadence-child' ),               'default' => 'RDC', 'cb' => 'sanitize_text_field' ),
        'amidac_maps_url' => array( 'label' => __( 'Lien Google Maps', 'amidac-kadence-child' ),   'default' => '', 'cb' => 'esc_url_raw' ),
    );

    foreach ( $contact_fields as $id => $field ) {
        $wp_customize->add_setting( $id, array(
            'default'           => $field['default'],
            'sanitize_callback' => $field['cb'],
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( $id, array(
            'label'   => $field['label'],
            'section' => 'amidac_contact',
            'type'    => isset( $field['type'] ) ? $field['type'] : 'text',
        ) );
    }

    // ========================================================================
    // SECTION : RÉSEAUX SOCIAUX
    // ========================================================================

    $wp_customize->add_section( 'amidac_social', array(
        'title'    => __( 'Réseaux Sociaux', 'amidac-kadence-child' ),
        'panel'    => 'amidac_panel',
        'priority' => 30,
    ) );

    $social_links = array(
        'amidac_facebook'  => __( 'Facebook URL',  'amidac-kadence-child' ),
        'amidac_twitter'   => __( 'Twitter / X URL', 'amidac-kadence-child' ),
        'amidac_instagram' => __( 'Instagram URL', 'amidac-kadence-child' ),
        'amidac_linkedin'  => __( 'LinkedIn URL',  'amidac-kadence-child' ),
        'amidac_youtube'   => __( 'YouTube URL',   'amidac-kadence-child' ),
        'amidac_tiktok'    => __( 'TikTok URL',    'amidac-kadence-child' ),
        'amidac_whatsapp'  => __( 'WhatsApp Numéro', 'amidac-kadence-child' ),
    );

    foreach ( $social_links as $id => $label ) {
        $wp_customize->add_setting( $id, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
            'transport'         => 'refresh',
        ) );
        $wp_customize->add_control( $id, array(
            'label'   => $label,
            'section' => 'amidac_social',
            'type'    => 'url',
        ) );
    }

    // ========================================================================
    // SECTION : BARRE D'ANNONCE (TOP BAR)
    // ========================================================================

    $wp_customize->add_section( 'amidac_topbar', array(
        'title'    => __( "Barre d'annonce", 'amidac-kadence-child' ),
        'panel'    => 'amidac_panel',
        'priority' => 40,
    ) );

    $wp_customize->add_setting( 'amidac_topbar_enable', array(
        'default'           => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ) );
    $wp_customize->add_control( 'amidac_topbar_enable', array(
        'label'   => __( "Afficher la barre d'annonce", 'amidac-kadence-child' ),
        'section' => 'amidac_topbar',
        'type'    => 'checkbox',
    ) );

    $wp_customize->add_setting( 'amidac_topbar_message', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ) );
    $wp_customize->add_control( 'amidac_topbar_message', array(
        'label'   => __( "Message d'annonce", 'amidac-kadence-child' ),
        'section' => 'amidac_topbar',
        'type'    => 'textarea',
    ) );

    // ========================================================================
    // SECTION : CTA GLOBAL
    // ========================================================================

    $wp_customize->add_section( 'amidac_cta', array(
        'title'    => __( "Appel à l'action (CTA)", 'amidac-kadence-child' ),
        'panel'    => 'amidac_panel',
        'priority' => 50,
    ) );

    $wp_customize->add_setting( 'amidac_cta_text', array(
        'default'           => __( 'Faire un don', 'amidac-kadence-child' ),
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ) );
    $wp_customize->add_control( 'amidac_cta_text', array(
        'label'   => __( 'Texte du bouton CTA', 'amidac-kadence-child' ),
        'section' => 'amidac_cta',
        'type'    => 'text',
    ) );

    $wp_customize->add_setting( 'amidac_cta_url', array(
        'default'           => '#don',
        'sanitize_callback' => 'esc_url_raw',
    ) );
    $wp_customize->add_control( 'amidac_cta_url', array(
        'label'   => __( 'URL du bouton CTA', 'amidac-kadence-child' ),
        'section' => 'amidac_cta',
        'type'    => 'url',
    ) );

    // ========================================================================
    // SECTION : COULEURS AVANCÉES
    // ========================================================================

    $wp_customize->add_section( 'amidac_colors', array(
        'title'    => __( 'Couleurs AMIDAC', 'amidac-kadence-child' ),
        'panel'    => 'amidac_panel',
        'priority' => 60,
    ) );

    $color_settings = array(
        'amidac_color_primary'   => array( 'label' => __( 'Couleur principale', 'amidac-kadence-child' ), 'default' => '#005A9C' ),
        'amidac_color_secondary' => array( 'label' => __( 'Couleur accent',     'amidac-kadence-child' ), 'default' => '#F5A623' ),
        'amidac_color_dark'      => array( 'label' => __( 'Couleur sombre',     'amidac-kadence-child' ), 'default' => '#1A1A2E' ),
    );

    foreach ( $color_settings as $id => $setting ) {
        $wp_customize->add_setting( $id, array(
            'default'           => $setting['default'],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ) );
        $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, array(
            'label'   => $setting['label'],
            'section' => 'amidac_colors',
        ) ) );
    }
}
add_action( 'customize_register', 'amidac_customizer_settings' );

/**
 * CSS dynamique basé sur les réglages du Customizer.
 */
function amidac_customizer_dynamic_css() {
    $primary   = get_theme_mod( 'amidac_color_primary',   '#005A9C' );
    $secondary = get_theme_mod( 'amidac_color_secondary', '#F5A623' );
    $dark      = get_theme_mod( 'amidac_color_dark',      '#1A1A2E' );

    echo '<style id="amidac-dynamic-css">
    :root {
        --amidac-primary:   ' . sanitize_hex_color( $primary )   . ';
        --amidac-secondary: ' . sanitize_hex_color( $secondary ) . ';
        --amidac-dark:      ' . sanitize_hex_color( $dark )      . ';
    }
    </style>';
}
add_action( 'wp_head', 'amidac_customizer_dynamic_css' );
