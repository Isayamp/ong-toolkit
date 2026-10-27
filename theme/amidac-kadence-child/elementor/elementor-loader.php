<?php
/**
 * AMIDAC — Chargeur des Widgets Elementor
 *
 * @package AMIDAC_Kadence_Child
 */

defined( 'ABSPATH' ) || exit;

final class AMIDAC_Elementor_Loader {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'elementor/widgets/register',              array( $this, 'register_widgets' ) );
        add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'enqueue_styles' ) );
        add_action( 'elementor/elements/categories_registered',array( $this, 'register_category' ) );
    }

    public function register_category( $elements_manager ) {
        $elements_manager->add_category(
            'amidac-widgets',
            array(
                'title' => __( '🏢 AMIDAC asbl', 'amidac-kadence-child' ),
                'icon'  => 'fa fa-plug',
            )
        );
    }

    public function register_widgets( $widgets_manager ) {
        $file = AMIDAC_CHILD_DIR . '/elementor/widgets/widget-projets-grid.php';
        if ( file_exists( $file ) ) {
            require_once $file;
        }
        if ( class_exists( 'AMIDAC_Widget_Projets_Grid' ) ) {
            $widgets_manager->register( new AMIDAC_Widget_Projets_Grid() );
        }
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            'amidac-elementor-widgets',
            AMIDAC_CHILD_URI . '/elementor/css/elementor-widgets.css',
            array(),
            AMIDAC_CHILD_VERSION
        );
    }
}

/**
 * Hook natif Elementor — garanti de se déclencher après le chargement complet.
 */
add_action( 'elementor/loaded', function() {
    AMIDAC_Elementor_Loader::instance();
} );

/**
 * Avertissement admin si Elementor absent.
 */
add_action( 'admin_notices', function() {
    if ( ! did_action( 'elementor/loaded' ) ) {
        echo '<div class="notice notice-warning is-dismissible">'
           . '<p><strong>AMIDAC Child Theme :</strong> '
           . esc_html__( "Les widgets AMIDAC nécessitent l'extension Elementor.", 'amidac-kadence-child' )
           . '</p></div>';
    }
} );