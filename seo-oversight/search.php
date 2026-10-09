<?php
/**
 * Template Name: Search Results Page
 */

get_header();
?>

<div class="container section-padding">
    <div class="section-header">
        <h1>نتایج جستجو برای: «<?php echo get_search_query(); ?>»</h1>
        <p>نمایش لیست نوشته‌ها و برگه متناسب با عبارات جستجو شده</p>
    </div>

    <div style="max-width:800px; margin:0 auto 2rem auto;">
        <?php get_search_form(); ?>
    </div>

    <div class="seo-grid">
        <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
            <article class="seo-card">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div style="font-size:0.8rem; color:var(--muted-text); margin-bottom:0.5rem;">
                    نوع نوشته: <?php echo get_post_type(); ?> | تاریخ: <?php echo get_the_date(); ?>
                </div>
                <p class="justify-text" style="font-size:0.9rem; color:var(--muted-text);"><?php echo wp_trim_words( get_the_excerpt(), 22 ); ?></p>
                <a href="<?php the_permalink(); ?>" class="seo-btn seo-btn-sm seo-btn-outline">مشاهده جزییات &larr;</a>
            </article>
        <?php endwhile; else : ?>
            <div class="seo-card" style="grid-column: 1 / -1; text-align:center;">
                <h3>هیچ نتیجه‌ای برای عبارت موردنظر یافت نشد!</h3>
                <p>لطفاً عبارت دیگری را جستجو کنید یا به <a href="<?php echo esc_url( home_url('/') ); ?>">صفحه اصلی</a> بازگردید.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php get_footer(); ?>
