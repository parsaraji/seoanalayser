<?php
/**
 * Template Name: Generic Content Page
 */

get_header();
?>

<div class="container section-padding">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'seo-card' ); ?>>
            <h1><?php the_title(); ?></h1>
            <hr style="margin: 1rem 0 2rem 0; border: 0; border-top: 1px solid var(--border-color);">

            <div class="seo-article-content">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
