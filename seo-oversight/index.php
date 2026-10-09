<?php
/**
 * Main Template File (Fallback)
 */

get_header();
?>

<div class="container section-padding">
    <div class="section-header">
        <h1><?php bloginfo( 'name' ); ?></h1>
        <p><?php bloginfo( 'description' ); ?></p>
    </div>

    <div class="seo-grid">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <article class="seo-card">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div style="font-size:0.8rem; color:var(--muted-text); margin-bottom:0.5rem;">
                    تاریخ: <?php echo get_the_date(); ?> | نویسنده: <?php the_author(); ?>
                </div>
                <p class="justify-text" style="font-size:0.9rem; color:var(--muted-text);"><?php echo wp_trim_words( get_the_excerpt(), 22 ); ?></p>
                <a href="<?php the_permalink(); ?>" class="seo-btn seo-btn-sm seo-btn-outline">مطالعه بیشتر &larr;</a>
            </article>
        <?php endwhile; else : ?>
            <div class="seo-card" style="grid-column: 1 / -1; text-align:center;">
                <h3>مطلوبی یافت نشد.</h3>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
