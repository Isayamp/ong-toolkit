<?php
/**
 * Vidage des caches quand les documents ou le plugin changent.
 *
 * Le contenu des rapports vit dans un type non public : ni WordPress ni les extensions de cache
 * ne savent que la page Rapports & Publications en dépend. Sans ce vidage, la page garde l'ancien
 * affichage (cache des éléments Elementor, cache de pages) après un ajout ou une mise à jour.
 */

defined( 'ABSPATH' ) || exit;

function amidac_toolkit_purger_cache() {
    // Elementor : cache des éléments et fichiers CSS générés.
    if ( did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
    }

    // Extensions de cache courantes, quand elles sont présentes.
    do_action( 'litespeed_purge_all' );
    if ( function_exists( 'rocket_clean_domain' ) ) {
        rocket_clean_domain();
    }
    if ( function_exists( 'w3tc_flush_all' ) ) {
        w3tc_flush_all();
    }
    if ( function_exists( 'wp_cache_clear_cache' ) ) {
        wp_cache_clear_cache();
    }
}

/**
 * Après une modification d'un document : un seul vidage en fin de requête, même pour un import de 50 fichiers.
 */
function amidac_toolkit_purger_plus_tard() {
    static $prevu = false;
    if ( ! $prevu ) {
        $prevu = true;
        add_action( 'shutdown', 'amidac_toolkit_purger_cache' );
    }
}

function amidac_toolkit_purger_si_rapport( $post_id ) {
    if ( 'amidac_rapport' === get_post_type( $post_id ) ) {
        amidac_toolkit_purger_plus_tard();
    }
}
add_action( 'save_post_amidac_rapport', 'amidac_toolkit_purger_plus_tard' );
add_action( 'before_delete_post', 'amidac_toolkit_purger_si_rapport' );

/**
 * Après une mise à jour des fichiers du plugin (déploiement) : un seul vidage par version.
 */
function amidac_toolkit_purger_apres_deploiement() {
    $signature = AMIDAC_TOOLKIT_VERSION;
    foreach ( array( 'includes/shortcodes.php', 'assets/css/toolkit.css', 'assets/js/rapports-front.js' ) as $fichier ) {
        $signature .= '.' . (int) filemtime( AMIDAC_TOOLKIT_DIR . $fichier );
    }

    if ( get_option( 'amidac_toolkit_signature' ) !== $signature ) {
        update_option( 'amidac_toolkit_signature', $signature, false );
        add_action( 'wp_loaded', 'amidac_toolkit_purger_cache' );
    }
}
add_action( 'init', 'amidac_toolkit_purger_apres_deploiement' );
