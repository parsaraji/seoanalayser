<?php
get_header();
?>

<div class="container section-padding">
    <div class="section-header">
        <h1><?php the_archive_title(); ?></h1>
        <p><?php the_archive_description(); ?></p>
    </div>

    <div class="seo-grid">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <article class="seo-card">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <p class="justify-text" style="font-size:0.9rem; color:var(--muted-text);"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>
                <a href="<?php the_permalink(); ?>" class="seo-btn seo-btn-sm seo-btn-outline">مطالعه مقاله &larr;</a>
            </article>
        <?php endwhile; endif; ?>
    </div>
</div>

<?php get_footer(); ?>
