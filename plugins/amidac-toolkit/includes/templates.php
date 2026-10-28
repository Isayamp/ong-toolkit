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
 * Carte d'un projet : image en fond, dégradé, pastille du secteur et titre par-dessus.
 */
function amidac_toolkit_carte_projet( $post_id = null ) {
    $post_id  = $post_id ? $post_id : get_the_ID();
    $secteurs = get_the_terms( $post_id, 'secteur' );
    $secteur  = ( $secteurs && ! is_wp_error( $secteurs ) ) ? $secteurs[0]->name : get_post_meta( $post_id, '_amidac_secteur_label', true );
    $couleur  = get_post_meta( $post_id, '_amidac_tag_couleur', true );
    $couleur  = $couleur ? $couleur : 'bleu';
    ?>
    <a class="amidac-carte" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
        <?php if ( has_post_thumbnail( $post_id ) ) : ?>
            <?php echo get_the_post_thumbnail( $post_id, 'medium_large', array( 'class' => 'amidac-carte__image' ) ); ?>
        <?php endif; ?>
        <span class="amidac-carte__texte">
            <?php if ( $secteur ) : ?>
                <span class="amidac-pill amidac-pill-<?php echo esc_attr( $couleur ); ?>"><?php echo esc_html( $secteur ); ?></span>
            <?php endif; ?>
            <span class="amidac-carte__titre"><?php echo esc_html( get_the_title( $post_id ) ); ?></span>
        </span>
    </a>
    <?php
}

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
