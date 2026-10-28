<?php
/**
 * Intégration Elementor : catégorie et widgets.
 * Accroché sur elementor/loaded : rien n'est chargé si Elementor est absent.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'elementor/loaded', function () {

    add_action( 'elementor/elements/categories_registered', function ( $manager ) {
        $manager->add_category( 'amidac-widgets', array(
            'title' => __( 'AMIDAC asbl', 'amidac-toolkit' ),
            'icon'  => 'fa fa-plug',
        ) );
    } );

    add_action( 'elementor/widgets/register', function ( $manager ) {
        require_once AMIDAC_TOOLKIT_DIR . 'widgets/widget-projets-grid.php';
        $manager->register( new AMIDAC_Widget_Projets_Grid() );
    } );

    $enqueue = function () {
        $css = AMIDAC_TOOLKIT_DIR . 'assets/css/widgets.css';
        wp_enqueue_style( 'amidac-widgets', AMIDAC_TOOLKIT_URI . 'assets/css/widgets.css', array(), AMIDAC_TOOLKIT_VERSION . '.' . filemtime( $css ) );
    };
    add_action( 'elementor/frontend/after_enqueue_styles', $enqueue );
    add_action( 'elementor/editor/after_enqueue_styles', $enqueue );
} );
