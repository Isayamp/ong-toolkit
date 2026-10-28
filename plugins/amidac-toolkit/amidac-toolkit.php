<?php
/**
 * Plugin Name: AMIDAC Toolkit
 * Description: Projets, équipe, partenaires, rapports, gabarits et widgets Elementor pour les sites d'ONG.
 * Version:     1.0.0
 * Author:      Isaya M'lela Pombo — Emo Touch Group
 * License:     GPL-2.0-or-later
 * Text Domain: amidac-toolkit
 * Requires PHP: 7.4
 */

defined( 'ABSPATH' ) || exit;

define( 'AMIDAC_TOOLKIT_VERSION', '1.0.0' );
define( 'AMIDAC_TOOLKIT_FILE', __FILE__ );
define( 'AMIDAC_TOOLKIT_DIR', plugin_dir_path( __FILE__ ) );
define( 'AMIDAC_TOOLKIT_URI', plugin_dir_url( __FILE__ ) );

$amidac_toolkit_modules = array(
    'post-types',
    'metaboxes',
    'rapports-admin',
    'rapports-import',
    'templates',
    'shortcodes',
    'elementor',
);

foreach ( $amidac_toolkit_modules as $module ) {
    require_once AMIDAC_TOOLKIT_DIR . 'includes/' . $module . '.php';
}

register_activation_hook( __FILE__, function () {
    amidac_toolkit_register_post_types();
    amidac_toolkit_register_taxonomies();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
