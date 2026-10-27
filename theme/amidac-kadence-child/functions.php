<?php
/**
 * AMIDAC — Kadence Child Theme
 * functions.php (MODIFIÉ)
 *
 * MODIFICATIONS:
 * - Footer customizations commentées (géré via Elementor custom widgets)
 * - Kadence hooks top_bar commentés (header custom dans Elementor)
 *
 * @package     AMIDAC_Kadence_Child
 * @author      Isaya M'lela Pombo — Emo Touch Group
 * @link        https://emotouchgroup.com
 * @version     1.0.0 (MODIFIED)
 */

defined( 'ABSPATH' ) || exit;

// ============================================================================
// CONSTANTES
// ============================================================================

define( 'AMIDAC_CHILD_VERSION',   '1.0.0' );
define( 'AMIDAC_CHILD_DIR',       get_stylesheet_directory() );
define( 'AMIDAC_CHILD_URI',       get_stylesheet_directory_uri() );
define( 'AMIDAC_PARENT_DIR',      get_template_directory() );
define( 'AMIDAC_PARENT_URI',      get_template_directory_uri() );
define( 'AMIDAC_THEME_NAME',      'AMIDAC asbl' );
define( 'AMIDAC_AGENCY',          'Emo Touch Group' );
define( 'AMIDAC_DEV',             'Isaya M\'lela Pombo' );

// ============================================================================
// CHARGEMENT DES STYLES & SCRIPTS
// ============================================================================

/**
 * Enqueue les styles du parent Kadence et du child theme.
 */
function amidac_enqueue_styles() {
    // Style du parent (Kadence)
    wp_enqueue_style(
        'kadence-theme-style',
        AMIDAC_PARENT_URI . '/style.css',
        array(),
        wp_get_theme( 'kadence' )->get( 'Version' )
    );

    // Polices Google Fonts
    wp_enqueue_style(
        'amidac-google-fonts',
        'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Open+Sans:ital,wght@0,400;0,600;1,400&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap',
        array(),
        null
    );

    // Style principal du child theme
    wp_enqueue_style(
        'amidac-child-style',
        AMIDAC_CHILD_URI . '/style.css',
        array( 'kadence-theme-style', 'amidac-google-fonts' ),
        AMIDAC_CHILD_VERSION
    );

    // Style custom additionnel
    if ( file_exists( AMIDAC_CHILD_DIR . '/assets/css/custom.css' ) ) {
        wp_enqueue_style(
            'amidac-custom',
            AMIDAC_CHILD_URI . '/assets/css/custom.css',
            array( 'amidac-child-style' ),
            AMIDAC_CHILD_VERSION
        );
    }
}
add_action( 'wp_enqueue_scripts', 'amidac_enqueue_styles' );

/**
 * Enqueue les scripts JS.
 */
function amidac_enqueue_scripts() {
    wp_enqueue_script(
        'amidac-main',
        AMIDAC_CHILD_URI . '/assets/js/main.js',
        array( 'jquery' ),
        AMIDAC_CHILD_VERSION,
        true
    );

    // Localisation pour AJAX
    wp_localize_script( 'amidac-main', 'amidacData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'amidac_nonce' ),
        'siteUrl' => get_site_url(),
        'themeUri' => AMIDAC_CHILD_URI,
    ) );
}
add_action( 'wp_enqueue_scripts', 'amidac_enqueue_scripts' );

/**
 * Styles d'administration.
 */
function amidac_admin_styles() {
    wp_enqueue_style(
        'amidac-admin',
        AMIDAC_CHILD_URI . '/assets/css/admin.css',
        array(),
        AMIDAC_CHILD_VERSION
    );
}
add_action( 'admin_enqueue_scripts', 'amidac_admin_styles' );

/**
 * Enqueue les styles des programmes.
 */
function amidac_enqueue_programs_styles() {
    wp_enqueue_style(
        'amidac-programs',
        AMIDAC_CHILD_URI . '/assets/css/programs.css',
        array( 'amidac-custom' ),
        AMIDAC_CHILD_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'amidac_enqueue_programs_styles' );


// ============================================================================
// SUPPORT DES FONCTIONNALITÉS WORDPRESS
// ============================================================================

function amidac_theme_support() {
    // Titre dans la balise <head>
    add_theme_support( 'title-tag' );

    // Images mises en avant
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 1200, 630, true );

    // Formats de post
    add_theme_support( 'post-formats', array(
        'aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio'
    ) );

    // Customisation du logo
    add_theme_support( 'custom-logo', array(
        'height'      => 80,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
        'header-text' => array( 'site-title', 'site-description' ),
    ) );

    // HTML5
    add_theme_support( 'html5', array(
        'comment-list', 'comment-form', 'search-form',
        'gallery', 'caption', 'style', 'script',
    ) );

    // Bloc Editor — désactiver les styles par défaut
    add_theme_support( 'disable-custom-colors' );
    add_theme_support( 'editor-color-palette', array(
        array( 'name' => 'Bleu AMIDAC',    'slug' => 'amidac-primary',   'color' => '#005A9C' ),
        array( 'name' => 'Or AMIDAC',      'slug' => 'amidac-secondary', 'color' => '#F5A623' ),
        array( 'name' => 'Foncé AMIDAC',   'slug' => 'amidac-dark',      'color' => '#1A1A2E' ),
        array( 'name' => 'Clair AMIDAC',   'slug' => 'amidac-light',     'color' => '#F7F9FC' ),
        array( 'name' => 'Blanc',          'slug' => 'white',            'color' => '#FFFFFF' ),
        array( 'name' => 'Succès',         'slug' => 'success',          'color' => '#27AE60' ),
        array( 'name' => 'Danger',         'slug' => 'danger',           'color' => '#E74C3C' ),
    ) );

    // Tailles de polices dans l'éditeur
    add_theme_support( 'editor-font-sizes', array(
        array( 'name' => 'Petit',       'shortName' => 'S',  'size' => 14, 'slug' => 'small'  ),
        array( 'name' => 'Normal',      'shortName' => 'M',  'size' => 16, 'slug' => 'normal' ),
        array( 'name' => 'Grand',       'shortName' => 'L',  'size' => 20, 'slug' => 'large'  ),
        array( 'name' => 'Très grand',  'shortName' => 'XL', 'size' => 28, 'slug' => 'xlarge' ),
        array( 'name' => 'Énorme',      'shortName' => 'XX', 'size' => 40, 'slug' => 'huge'   ),
    ) );

    // Wide alignment
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor-style.css' );
}
add_action( 'after_setup_theme', 'amidac_theme_support' );

// ============================================================================
// ZONES DE NAVIGATION
// ============================================================================

function amidac_register_menus() {
    register_nav_menus( array(
        'primary'   => __( 'Menu Principal',   'amidac-kadence-child' ),
        'secondary' => __( 'Menu Secondaire',  'amidac-kadence-child' ),
        'footer'    => __( 'Menu Pied de page','amidac-kadence-child' ),
        'social'    => __( 'Réseaux Sociaux',  'amidac-kadence-child' ),
        'legal'     => __( 'Mentions légales', 'amidac-kadence-child' ),
    ) );
}
add_action( 'after_setup_theme', 'amidac_register_menus' );

// ============================================================================
// ZONES DE WIDGETS
// ============================================================================

function amidac_register_sidebars() {
    $defaults = array(
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    );

    register_sidebar( array_merge( $defaults, array(
        'name'        => __( 'Barre latérale principale', 'amidac-kadence-child' ),
        'id'          => 'sidebar-1',
        'description' => __( 'Widgets affichés dans la barre latérale.', 'amidac-kadence-child' ),
    ) ) );

    register_sidebar( array_merge( $defaults, array(
        'name'        => __( 'Pied de page — Colonne 1', 'amidac-kadence-child' ),
        'id'          => 'footer-1',
        'description' => __( 'Première colonne du footer.', 'amidac-kadence-child' ),
    ) ) );

    register_sidebar( array_merge( $defaults, array(
        'name'        => __( 'Pied de page — Colonne 2', 'amidac-kadence-child' ),
        'id'          => 'footer-2',
        'description' => __( 'Deuxième colonne du footer.', 'amidac-kadence-child' ),
    ) ) );

    register_sidebar( array_merge( $defaults, array(
        'name'        => __( 'Pied de page — Colonne 3', 'amidac-kadence-child' ),
        'id'          => 'footer-3',
        'description' => __( 'Troisième colonne du footer.', 'amidac-kadence-child' ),
    ) ) );

    register_sidebar( array_merge( $defaults, array(
        'name'        => __( 'Pied de page — Colonne 4', 'amidac-kadence-child' ),
        'id'          => 'footer-4',
        'description' => __( 'Quatrième colonne du footer.', 'amidac-kadence-child' ),
    ) ) );

    register_sidebar( array_merge( $defaults, array(
        'name'        => __( 'Avant contenu — Page d\'accueil', 'amidac-kadence-child' ),
        'id'          => 'home-before-content',
        'description' => __( 'Zone avant le contenu de la page d\'accueil.', 'amidac-kadence-child' ),
    ) ) );
}
add_action( 'widgets_init', 'amidac_register_sidebars' );

// ============================================================================
// TYPES DE POSTS PERSONNALISÉS (CPT)
// ============================================================================

/**
 * Projets / Programmes
 */
function amidac_register_cpt_projets() {
    $labels = array(
        'name'               => __( 'Projets',           'amidac-kadence-child' ),
        'singular_name'      => __( 'Projet',            'amidac-kadence-child' ),
        'menu_name'          => __( 'Projets',           'amidac-kadence-child' ),
        'add_new'            => __( 'Ajouter',           'amidac-kadence-child' ),
        'add_new_item'       => __( 'Ajouter un projet', 'amidac-kadence-child' ),
        'edit_item'          => __( 'Modifier le projet','amidac-kadence-child' ),
        'view_item'          => __( 'Voir le projet',    'amidac-kadence-child' ),
        'all_items'          => __( 'Tous les projets',  'amidac-kadence-child' ),
        'search_items'       => __( 'Rechercher',        'amidac-kadence-child' ),
        'not_found'          => __( 'Aucun projet trouvé.', 'amidac-kadence-child' ),
    );

    register_post_type( 'amidac_projet', array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-portfolio',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'rewrite'            => array( 'slug' => 'projets' ),
        'show_in_rest'       => true,
    ) );
}
add_action( 'init', 'amidac_register_cpt_projets' );

/**
 * Équipe / Membres
 */
function amidac_register_cpt_equipe() {
    $labels = array(
        'name'          => __( 'Équipe',           'amidac-kadence-child' ),
        'singular_name' => __( 'Membre',           'amidac-kadence-child' ),
        'menu_name'     => __( 'Équipe',           'amidac-kadence-child' ),
        'add_new_item'  => __( 'Ajouter un membre','amidac-kadence-child' ),
        'edit_item'     => __( 'Modifier membre',  'amidac-kadence-child' ),
        'all_items'     => __( 'Tous les membres', 'amidac-kadence-child' ),
    );

    register_post_type( 'amidac_membre', array(
        'labels'       => $labels,
        'public'       => true,
        'has_archive'  => false,
        'menu_position'=> 6,
        'menu_icon'    => 'dashicons-groups',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'rewrite'      => array( 'slug' => 'equipe' ),
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'amidac_register_cpt_equipe' );

/**
 * Partenaires
 */
function amidac_register_cpt_partenaires() {
    register_post_type( 'amidac_partenaire', array(
        'labels' => array(
            'name'          => __( 'Partenaires',          'amidac-kadence-child' ),
            'singular_name' => __( 'Partenaire',           'amidac-kadence-child' ),
            'all_items'     => __( 'Tous les partenaires', 'amidac-kadence-child' ),
            'add_new_item'  => __( 'Ajouter un partenaire','amidac-kadence-child' ),
        ),
        'public'       => false,
        'show_ui'      => true,
        'menu_position'=> 7,
        'menu_icon'    => 'dashicons-businessman',
        'supports'     => array( 'title', 'thumbnail', 'custom-fields' ),
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'amidac_register_cpt_partenaires' );

/**
 * Témoignages / Rapports
 */
function amidac_register_cpt_rapports() {
    register_post_type( 'amidac_rapport', array(
        'labels' => array(
            'name'          => __( 'Rapports',         'amidac-kadence-child' ),
            'singular_name' => __( 'Rapport',          'amidac-kadence-child' ),
            'all_items'     => __( 'Tous les rapports','amidac-kadence-child' ),
            'add_new_item'  => __( 'Ajouter un rapport','amidac-kadence-child' ),
        ),
        'public'       => true,
        'has_archive'  => true,
        'menu_position'=> 8,
        'menu_icon'    => 'dashicons-media-document',
        'supports'     => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        'rewrite'      => array( 'slug' => 'rapports' ),
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'amidac_register_cpt_rapports' );

// ============================================================================
// TAXONOMIES PERSONNALISÉES
// ============================================================================

function amidac_register_taxonomies() {
    // Secteurs pour les projets
    register_taxonomy( 'secteur', array( 'amidac_projet' ), array(
        'label'        => __( 'Secteurs', 'amidac-kadence-child' ),
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'secteur' ),
        'show_in_rest' => true,
    ) );
    
    // Programmes pour les projets
    register_taxonomy( 'programme', array( 'amidac_projet' ), array(
        'label'        => __( 'Programmes', 'amidac-kadence-child' ),
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'programme' ),
        'show_in_rest' => true,
    ) );

    // Statuts de projet
    register_taxonomy( 'statut_projet', array( 'amidac_projet' ), array(
        'label'        => __( 'Statuts', 'amidac-kadence-child' ),
        'hierarchical' => false,
        'rewrite'      => array( 'slug' => 'statut' ),
        'show_in_rest' => true,
    ) );

    // Département pour les membres
    register_taxonomy( 'departement', array( 'amidac_membre' ), array(
        'label'        => __( 'Départements', 'amidac-kadence-child' ),
        'hierarchical' => true,
        'rewrite'      => array( 'slug' => 'departement' ),
        'show_in_rest' => true,
    ) );
}
add_action( 'init', 'amidac_register_taxonomies' );

// ============================================================================
// INCLUDES — MODULES ADDITIONNELS
// ============================================================================

$amidac_includes = array(
    '/inc/customizer.php',
    '/inc/helpers.php',
    '/inc/shortcodes.php',
    '/inc/metaboxes.php',
    '/inc/admin.php',
    '/inc/programs.php',
);

foreach ( $amidac_includes as $file ) {
    $path = AMIDAC_CHILD_DIR . $file;
    if ( file_exists( $path ) ) {
        require_once $path;
    }
}

// ============================================================================
// CUSTOMISATION DU PIED DE PAGE & HEADER
// ============================================================================
// NOTE: Footer et header gérés entièrement via Elementor (widgets HTML custom)
// Les customisations suivantes ont été DÉACTIVÉES pour éviter les conflits

/*
function amidac_custom_footer_credits() {
    $year = date( 'Y' );
    echo sprintf(
        '<div class="amidac-footer-credits">© %s <strong>%s</strong> — Développé avec ♥ par <a href="https://emotouchgroup.com" target="_blank" rel="noopener">%s</a></div>',
        esc_html( $year ),
        esc_html( AMIDAC_THEME_NAME ),
        esc_html( AMIDAC_AGENCY )
    );
}
*/

// ============================================================================
// KADENCE — INTÉGRATION HOOKS SPÉCIFIQUES
// ============================================================================
// DÉACTIVÉ: Les hooks suivants sont commentés car footer et header
// sont gérés via widgets Elementor custom

/*
function amidac_kadence_top_bar() {
    // Déactivé - header custom intégré dans Elementor
    // echo '<div class="amidac-top-bar">' . get_option('amidac_top_bar_message', '') . '</div>';
}
add_action( 'kadence_top_bar', 'amidac_kadence_top_bar' );

function amidac_kadence_options( $options ) {
    // Déactivé
    // $options['content_width'] = 'normal';
    return $options;
}
add_filter( 'kadence_options_defaults', 'amidac_kadence_options' );
*/

// ============================================================================
// EXCERPT — PERSONNALISATION
// ============================================================================

add_filter( 'excerpt_length', function() { return 30; }, 999 );
add_filter( 'excerpt_more',   function() {
    return ' <a class="read-more" href="' . get_permalink() . '">' .
           __( 'Lire la suite →', 'amidac-kadence-child' ) . '</a>';
} );

// ============================================================================
// AJAX — EXEMPLE DE HANDLER
// ============================================================================

function amidac_ajax_contact_handler() {
    check_ajax_referer( 'amidac_nonce', 'nonce' );
    // Traitement du formulaire de contact via AJAX
    wp_send_json_success( array( 'message' => __( 'Message envoyé !', 'amidac-kadence-child' ) ) );
}
add_action( 'wp_ajax_amidac_contact',        'amidac_ajax_contact_handler' );
add_action( 'wp_ajax_nopriv_amidac_contact', 'amidac_ajax_contact_handler' );

// ============================================================================
// SÉCURITÉ DE BASE
// ============================================================================

// Masquer la version de WordPress
remove_action( 'wp_head', 'wp_generator' );

// Désactiver XML-RPC (sauf si nécessaire)
add_filter( 'xmlrpc_enabled', '__return_false' );

// Supprimer les liens inutiles dans <head>
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

// Désactiver les emojis WP (performance)
function amidac_disable_emojis() {
    remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles',     'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles',  'print_emoji_styles' );
    remove_filter( 'the_content_feed',    'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss',    'wp_staticize_emoji' );
    remove_filter( 'wp_mail',             'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'amidac_disable_emojis' );

// ============================================================================
// PERFORMANCE — CLEANUP <head>
// ============================================================================

function amidac_clean_head() {
    remove_action( 'wp_head', 'feed_links',                       2 );
    remove_action( 'wp_head', 'feed_links_extra',                 3 );
    remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10 );
}
add_action( 'init', 'amidac_clean_head' );

// ============================================================================
// TAILLES D'IMAGES PERSONNALISÉES
// ============================================================================

function amidac_add_image_sizes() {
    add_image_size( 'amidac-hero',    1920, 800, true );
    add_image_size( 'amidac-card',     600, 400, true );
    add_image_size( 'amidac-thumb',    400, 300, true );
    add_image_size( 'amidac-square',   300, 300, true );
    add_image_size( 'amidac-portrait', 400, 500, true );
}
add_action( 'after_setup_theme', 'amidac_add_image_sizes' );

// Exposer les tailles dans la sélection de l'éditeur
function amidac_custom_image_sizes_choices( $sizes ) {
    return array_merge( $sizes, array(
        'amidac-hero'    => __( 'AMIDAC — Hero',     'amidac-kadence-child' ),
        'amidac-card'    => __( 'AMIDAC — Card',     'amidac-kadence-child' ),
        'amidac-thumb'   => __( 'AMIDAC — Vignette', 'amidac-kadence-child' ),
        'amidac-square'  => __( 'AMIDAC — Carré',    'amidac-kadence-child' ),
        'amidac-portrait'=> __( 'AMIDAC — Portrait', 'amidac-kadence-child' ),
    ) );
}
add_filter( 'image_size_names_choose', 'amidac_custom_image_sizes_choices' );

// ============================================================================
// SEO — META DESCRIPTION PAR DÉFAUT
// ============================================================================

function amidac_meta_description() {
    if ( is_singular() ) {
        global $post;
        if ( $post->post_excerpt ) {
            $desc = strip_tags( $post->post_excerpt );
        } else {
            $desc = wp_trim_words( strip_tags( $post->post_content ), 30, '...' );
        }
        echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . PHP_EOL;
    }
}
// À activer seulement si aucun plugin SEO n'est installé
// add_action( 'wp_head', 'amidac_meta_description' );

// ============================================================================
// SVG UPLOAD SUPPORT
// ============================================================================

// Allow SVG upload to WordPress Media Library
function cc_mime_types($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

// Fix the thumbnail preview issue in the media library dashboard
function fix_svg_preview() {
    echo '<style type="text/css">
          .attachment-266x266, .thumbnail img { width: 100% !important; height: auto !important; }
          </style>';
}
add_action('admin_head', 'fix_svg_preview');