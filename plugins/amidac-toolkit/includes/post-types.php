<?php
/**
 * Types de contenu et taxonomies.
 * Les slugs sont identiques à ceux de l'ancien child theme : les contenus existants sont conservés.
 */

defined( 'ABSPATH' ) || exit;

function amidac_toolkit_register_post_types() {
    $td = 'amidac-toolkit';

    register_post_type( 'amidac_projet', array(
        'labels' => array(
            'name'          => __( 'Projets', $td ),
            'singular_name' => __( 'Projet', $td ),
            'add_new_item'  => __( 'Ajouter un projet', $td ),
            'edit_item'     => __( 'Modifier le projet', $td ),
            'view_item'     => __( 'Voir le projet', $td ),
            'all_items'     => __( 'Tous les projets', $td ),
            'search_items'  => __( 'Rechercher', $td ),
            'not_found'     => __( 'Aucun projet trouvé.', $td ),
        ),
        'public'        => true,
        'has_archive'   => true,
        'menu_position' => 5,
        'menu_icon'     => 'dashicons-portfolio',
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'rewrite'       => array( 'slug' => 'projets' ),
        'show_in_rest'  => true,
    ) );

    register_post_type( 'amidac_membre', array(
        'labels' => array(
            'name'          => __( 'Équipe', $td ),
            'singular_name' => __( 'Membre', $td ),
            'add_new_item'  => __( 'Ajouter un membre', $td ),
            'edit_item'     => __( 'Modifier le membre', $td ),
            'all_items'     => __( 'Tous les membres', $td ),
        ),
        'public'        => true,
        'has_archive'   => false,
        'menu_position' => 6,
        'menu_icon'     => 'dashicons-groups',
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ),
        'rewrite'       => array( 'slug' => 'equipe' ),
        'show_in_rest'  => true,
    ) );

    register_post_type( 'amidac_partenaire', array(
        'labels' => array(
            'name'          => __( 'Partenaires', $td ),
            'singular_name' => __( 'Partenaire', $td ),
            'all_items'     => __( 'Tous les partenaires', $td ),
            'add_new_item'  => __( 'Ajouter un partenaire', $td ),
        ),
        'public'        => false,
        'show_ui'       => true,
        'menu_position' => 7,
        'menu_icon'     => 'dashicons-businessman',
        'supports'      => array( 'title', 'thumbnail', 'custom-fields', 'page-attributes' ),
        'show_in_rest'  => true,
    ) );

    register_post_type( 'amidac_rapport', array(
        'labels' => array(
            'name'          => __( 'Rapports', $td ),
            'singular_name' => __( 'Rapport', $td ),
            'all_items'     => __( 'Tous les rapports', $td ),
            'add_new_item'  => __( 'Ajouter un rapport', $td ),
        ),
        'public'        => true,
        'has_archive'   => true,
        'menu_position' => 8,
        'menu_icon'     => 'dashicons-media-document',
        'supports'      => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        'rewrite'       => array( 'slug' => 'rapports' ),
        'show_in_rest'  => true,
    ) );
}
add_action( 'init', 'amidac_toolkit_register_post_types' );

function amidac_toolkit_register_taxonomies() {
    $td = 'amidac-toolkit';

    $defs = array(
        'secteur'       => array( __( 'Secteurs', $td ),     true,  'secteur',     'amidac_projet' ),
        'programme'     => array( __( 'Programmes', $td ),   true,  'programme',   'amidac_projet' ),
        'statut_projet' => array( __( 'Statuts', $td ),      false, 'statut',      'amidac_projet' ),
        'departement'   => array( __( 'Départements', $td ), true,  'departement', 'amidac_membre' ),
    );

    foreach ( $defs as $name => $d ) {
        register_taxonomy( $name, array( $d[3] ), array(
            'label'        => $d[0],
            'hierarchical' => $d[1],
            'rewrite'      => array( 'slug' => $d[2] ),
            'show_in_rest' => true,
        ) );
    }
}
add_action( 'init', 'amidac_toolkit_register_taxonomies' );
