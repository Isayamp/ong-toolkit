<?php
/**
 * Fiche d'un projet.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
    the_post();
    $id = get_the_ID();
    $m  = function ( $k ) use ( $id ) {
        return get_post_meta( $id, '_amidac_' . $k, true );
    };

    $secteurs = get_the_terms( $id, 'secteur' );
    $secteur  = ( $secteurs && ! is_wp_error( $secteurs ) ) ? $secteurs[0]->name : $m( 'secteur_label' );
    $couleur  = $m( 'tag_couleur' ) ? $m( 'tag_couleur' ) : 'bleu';
    $statut   = amidac_toolkit_statut_label( $id );
    $fmt_date = function ( $v ) {
        return $v ? date_i18n( get_option( 'date_format' ), strtotime( $v ) ) : '';
    };

    $stats    = amidac_toolkit_stats_labels();
    $visibles = amidac_toolkit_stats_visibles( $id );

    $infos = array(
        __( 'Zone', 'amidac-toolkit' )        => $m( 'zone' ),
        __( 'Début', 'amidac-toolkit' )       => $fmt_date( $m( 'date_debut' ) ),
        __( 'Fin', 'amidac-toolkit' )         => $fmt_date( $m( 'date_fin' ) ),
        __( 'Partenaires', 'amidac-toolkit' ) => $m( 'partenaires' ),
        __( 'Financement', 'amidac-toolkit' ) => $m( 'financement' ),
        __( 'Budget', 'amidac-toolkit' )      => $m( 'budget_usd' ) ? number_format_i18n( (float) $m( 'budget_usd' ) ) . ' USD' : '',
    );

    $stats_html = '';
    foreach ( $stats as $key => $label ) {
        $v = $m( $key );
        if ( ! in_array( $key, $visibles, true ) || '' === $v || false === $v ) {
            continue;
        }
        $stats_html .= sprintf(
            '<div class="amidac-stat"><strong>%s%s</strong><span>%s</span></div>',
            esc_html( number_format_i18n( (float) $v ) ),
            'femmes_pct' === $key ? ' %' : '',
            esc_html( $label )
        );
    }
    ?>
    <article id="post-<?php the_ID(); ?>" <?php post_class( 'amidac-projet' ); ?>>

        <header class="amidac-projet__hero">
            <div class="amidac-projet__hero-text">
                <?php if ( $secteur ) : ?>
                    <span class="amidac-pill amidac-pill-<?php echo esc_attr( $couleur ); ?>"><?php echo esc_html( $secteur ); ?></span>
                <?php endif; ?>
                <?php if ( $statut ) : ?>
                    <span class="amidac-projet__statut"><?php echo esc_html( $statut ); ?></span>
                <?php endif; ?>
                <h1><?php the_title(); ?></h1>
            </div>
            <?php if ( has_post_thumbnail() ) : ?>
                <figure class="amidac-projet__figure">
                    <?php the_post_thumbnail( 'full', array( 'class' => 'amidac-projet__image' ) ); ?>
                </figure>
            <?php endif; ?>
        </header>

        <div class="amidac-projet__body">
            <?php if ( $stats_html ) : ?>
                <div class="amidac-stats"><?php echo $stats_html; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
            <?php endif; ?>

            <div class="amidac-projet__layout">
                <div class="amidac-projet__content entry-content">
                    <?php the_content(); ?>

                    <?php
                    $video = $m( 'video_url' );
                    $embed = $video ? wp_oembed_get( $video ) : false;
                    if ( $embed ) :
                        ?>
                        <div class="amidac-projet__video"><?php echo $embed; // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
                    <?php endif; ?>
                </div>

                <aside class="amidac-projet__infos">
                    <h2><?php esc_html_e( 'Résumé', 'amidac-toolkit' ); ?></h2>
                    <?php
                    // Extrait saisi, sinon extrait généré à partir du contenu.
                    $resume = trim( wp_strip_all_tags( get_the_excerpt() ) );
                    if ( $resume ) :
                        ?>
                        <p class="amidac-projet__resume"><?php echo esc_html( $resume ); ?></p>
                    <?php endif; ?>
                    <dl>
                        <?php foreach ( $infos as $label => $value ) : ?>
                            <?php if ( $value ) : ?>
                                <dt><?php echo esc_html( $label ); ?></dt>
                                <dd><?php echo esc_html( $value ); ?></dd>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </dl>
                    <?php if ( $m( 'rapport_url' ) ) : ?>
                        <a class="amidac-btn" href="<?php echo esc_url( $m( 'rapport_url' ) ); ?>" target="_blank" rel="noopener">
                            <?php esc_html_e( 'Télécharger le rapport', 'amidac-toolkit' ); ?>
                        </a>
                    <?php endif; ?>
                </aside>
            </div>
        </div>

        <?php
        $autres = new WP_Query(
            array(
                'post_type'           => 'amidac_projet',
                'posts_per_page'      => 3,
                'post__not_in'        => array( $id ),
                'ignore_sticky_posts' => true,
                'no_found_rows'       => true,
            )
        );
        if ( $autres->have_posts() ) :
            ?>
            <section class="amidac-projet__autres">
                <h2><?php esc_html_e( 'Autres projets', 'amidac-toolkit' ); ?></h2>
                <div class="amidac-grid">
                    <?php while ( $autres->have_posts() ) : $autres->the_post(); ?>
                        <a class="amidac-archive-card" href="<?php the_permalink(); ?>">
                            <?php
                            if ( has_post_thumbnail() ) {
                                the_post_thumbnail( 'medium_large' );
                            }
                            ?>
                            <div class="amidac-archive-card__body">
                                <h3><?php the_title(); ?></h3>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </section>
            <?php
            wp_reset_postdata();
        endif;
        ?>
    </article>
    <?php
endwhile;

get_footer();
