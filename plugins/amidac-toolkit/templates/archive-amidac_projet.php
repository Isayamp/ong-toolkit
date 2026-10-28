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
            <?php amidac_toolkit_carte_projet(); ?>
        <?php endwhile; ?>
    </div>
    <?php the_posts_pagination(); ?>
</main>
<?php
get_footer();
