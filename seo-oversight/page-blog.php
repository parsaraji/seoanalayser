<?php
/**
 * Template Name: Blog Index Archive
 */

get_header();
?>

<div class="container section-padding">
    <div class="section-header">
        <h1>وبلاگ تحلیلی و تخصصی نظارت سئو</h1>
        <p>مقالات، تحلیل‌های الگوریتمی، راهنماهای حسابرسی سئو و توصیه‌های مدیریت دیجیتال</p>
    </div>

    <div class="seo-grid">
        <?php
        $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
        $blog_query = new WP_Query( array(
            'post_type' => 'post',
            'posts_per_page' => 9,
            'paged' => $paged
        ) );

        if ( $blog_query->have_posts() ) :
            while ( $blog_query->have_posts() ) : $blog_query->the_post();
        ?>
            <article class="seo-card">
                <?php if ( has_post_thumbnail() ) : ?>
                    <div style="margin-bottom:1rem; overflow:hidden; border-radius:6px; max-height:180px;">
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium', array('style'=>'width:100%; height:auto; object-fit:cover;')); ?></a>
                    </div>
                <?php endif; ?>

                <div style="font-size:0.8rem; color:var(--muted-text); margin-bottom:0.5rem;">
                    <?php echo get_the_date(); ?> | نویسنده: <?php the_author(); ?>
                </div>

                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

                <p class="justify-text" style="font-size:0.9rem; color:var(--muted-text);">
                    <?php echo wp_trim_words( get_the_excerpt(), 22, '...' ); ?>
                </p>

                <a href="<?php the_permalink(); ?>" class="seo-btn seo-btn-sm seo-btn-outline" style="margin-top:10px;">ادامه مطلب &larr;</a>
            </article>
        <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<p>هنوز مقاله‌ای منتشر نشده است.</p>';
        endif;
        ?>
    </div>
</div>

<?php get_footer(); ?>
