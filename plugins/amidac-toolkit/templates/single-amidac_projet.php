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

    $stats = array(
        'beneficiaires' => __( 'Bénéficiaires directs', 'amidac-toolkit' ),
        'menages'       => __( 'Ménages touchés', 'amidac-toolkit' ),
        'femmes_pct'    => __( '% de femmes', 'amidac-toolkit' ),
        'aires_sante'   => __( 'Aires de santé', 'amidac-toolkit' ),
        'villages'      => __( 'Villages couverts', 'amidac-toolkit' ),
    );

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
        if ( '' === $v || false === $v ) {
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
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'full', array( 'class' => 'amidac-projet__image' ) ); ?>
            <?php endif; ?>
            <div class="amidac-projet__hero-text">
                <?php if ( $secteur ) : ?>
                    <span class="amidac-pill amidac-pill-<?php echo esc_attr( $couleur ); ?>"><?php echo esc_html( $secteur ); ?></span>
                <?php endif; ?>
                <?php if ( $statut ) : ?>
                    <span class="amidac-projet__statut"><?php echo esc_html( $statut ); ?></span>
                <?php endif; ?>
                <h1><?php the_title(); ?></h1>
            </div>
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
                    <h2><?php esc_html_e( 'En bref', 'amidac-toolkit' ); ?></h2>
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
    </article>
    <?php
endwhile;

get_footer();
