<?php
/**
 * Gabarits front-end des projets.
 * Un thème peut surcharger en plaçant single-amidac_projet.php / archive-amidac_projet.php à sa racine.
 */

defined( 'ABSPATH' ) || exit;

function amidac_toolkit_template( $template ) {
    $map = array(
        'single-amidac_projet.php'  => is_singular( 'amidac_projet' ),
        'archive-amidac_projet.php' => is_post_type_archive( 'amidac_projet' ),
    );

    foreach ( $map as $file => $applies ) {
        if ( $applies && ! locate_template( $file ) ) {
            return AMIDAC_TOOLKIT_DIR . 'templates/' . $file;
        }
    }
    return $template;
}
add_filter( 'template_include', 'amidac_toolkit_template' );

function amidac_toolkit_enqueue() {
    wp_enqueue_style( 'amidac-toolkit', AMIDAC_TOOLKIT_URI . 'assets/css/toolkit.css', array(), AMIDAC_TOOLKIT_VERSION );
}
add_action( 'wp_enqueue_scripts', 'amidac_toolkit_enqueue' );

/**
 * Libellé lisible du statut d'un projet.
 */
function amidac_toolkit_statut_label( $post_id ) {
    $terms = get_the_terms( $post_id, 'statut_projet' );
    if ( $terms && ! is_wp_error( $terms ) ) {
        return $terms[0]->name;
    }
    $labels = array(
        'en_cours' => __( 'En cours', 'amidac-toolkit' ),
        'termine'  => __( 'Terminé', 'amidac-toolkit' ),
        'suspendu' => __( 'Suspendu', 'amidac-toolkit' ),
    );
    $key = get_post_meta( $post_id, '_amidac_statut', true );
    return $labels[ $key ] ?? '';
}
