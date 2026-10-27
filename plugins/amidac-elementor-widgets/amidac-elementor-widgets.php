<?php
/**
 * Plugin Name:       AMIDAC — Widgets Elementor
 * Plugin URI:        https://amidac.org
 * Description:       Widgets Elementor personnalisés pour AMIDAC asbl. Grille de projets, statistiques, et plus.
 * Version:           1.0.0
 * Author:            Isaya M'lela Pombo — Emo Touch Group
 * Author URI:        https://emotouchgroup.com
 * License:           GPL v2+
 * Text Domain:       amidac-widgets
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Elementor tested up to: 3.20
 */
/** Composer of he without SASS. */


defined( 'ABSPATH' ) || exit;

define( 'AMIDAC_WIDGETS_VERSION', '1.0.0' );
define( 'AMIDAC_WIDGETS_DIR',     plugin_dir_path( __FILE__ ) );
define( 'AMIDAC_WIDGETS_URI',     plugin_dir_url( __FILE__ ) );

/**
 * Vérifier qu'Elementor est actif avant de charger quoi que ce soit.
 */
function amidac_widgets_check_elementor() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-error is-dismissible">'
               . '<p><strong>AMIDAC Widgets :</strong> Ce plugin nécessite <a href="https://wordpress.org/plugins/elementor/" target="_blank">Elementor</a> installé et activé.</p>'
               . '</div>';
        });
        return false;
    }
    return true;
}

/**
 * Point d'entrée principal — déclenché par Elementor lui-même.
 */
add_action( 'elementor/loaded', function() {

    // Catégorie personnalisée dans le panneau
    add_action( 'elementor/elements/categories_registered', function( $manager ) {
        $manager->add_category( 'amidac-widgets', array(
            'title' => '🏢 AMIDAC asbl',
            'icon'  => 'fa fa-plug',
        ));
    });

    // Enregistrement des widgets
    add_action( 'elementor/widgets/register', function( $manager ) {
        require_once AMIDAC_WIDGETS_DIR . 'widgets/widget-projets-grid.php';
        $manager->register( new AMIDAC_Widget_Projets_Grid() );
    });

    // CSS frontend
    add_action( 'elementor/frontend/after_enqueue_styles', function() {
        wp_enqueue_style(
            'amidac-widgets-css',
            AMIDAC_WIDGETS_URI . 'css/widgets.css',
            array(),
            AMIDAC_WIDGETS_VERSION
        );
    });

    // CSS dans l'éditeur aussi
    add_action( 'elementor/editor/after_enqueue_styles', function() {
        wp_enqueue_style(
            'amidac-widgets-editor-css',
            AMIDAC_WIDGETS_URI . 'css/widgets.css',
            array(),
            AMIDAC_WIDGETS_VERSION
        );
    });

});