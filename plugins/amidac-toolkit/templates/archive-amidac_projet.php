<?php
/**
 * Liste des projets.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<main class="amidac-archive">
    <h1><?php post_type_archive_title(); ?></h1>
    <div class="amidac-grid">
        <?php while ( have_posts() ) : the_post(); ?>
            <a class="amidac-archive-card" href="<?php the_permalink(); ?>">
                <?php
                if ( has_post_thumbnail() ) {
                    the_post_thumbnail( 'medium_large' );
                }
                ?>
                <div class="amidac-archive-card__body">
                    <h2><?php the_title(); ?></h2>
                    <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 25 ) ); ?></p>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
    <?php the_posts_pagination(); ?>
</main>
<?php
get_footer();
