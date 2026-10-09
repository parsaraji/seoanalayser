<?php
get_header();
?>

<div class="container section-padding">
    <div style="max-width:850px; margin:0 auto;">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class( 'seo-card' ); ?>>
                <div style="font-size:0.875rem; color:var(--muted-text); margin-bottom:0.75rem;">
                    دسته‌بندی: <?php the_category( ', ' ); ?> | تاریخ: <?php echo get_the_date(); ?> | نویسنده: <?php the_author(); ?>
                </div>

                <h1><?php the_title(); ?></h1>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div style="margin:1.5rem 0; border-radius:8px; overflow:hidden;">
                        <?php the_post_thumbnail( 'large', array( 'style' => 'width:100%; height:auto;' ) ); ?>
                    </div>
                <?php endif; ?>

                <div class="seo-article-content" style="margin-top:2rem;">
                    <?php the_content(); ?>
                </div>

                <hr style="margin:2.5rem 0 1.5rem 0; border:0; border-top:1px solid var(--border-color);">

                <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">
                    <div>برچسب‌ها: <?php the_tags( '', ', ', '' ); ?></div>
                    <div><a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="seo-btn seo-btn-sm seo-btn-outline">&rarr; بازگشت به وبلاگ</a></div>
                </div>
            </article>
        <?php endwhile; endif; ?>
    </div>
</div>

<?php get_footer(); ?>
