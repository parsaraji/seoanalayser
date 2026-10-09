<?php
/**
 * Template Name: Customer Dashboard
 */

get_header();
?>

<div class="container section-padding">
    <div class="section-header" style="margin-bottom:1.5rem;">
        <h1>داشبورد اختصاصی مدیریت و نظارت سئو</h1>
        <p>مشاهده وضعیت درخواست‌ها، پیش‌نویس و تایید قرارداد، ثبت واریزی‌ها و دریافت گزارش‌های دوره</p>
    </div>

    <?php
    if ( class_exists( 'SEO_OVERSIGHT_Dashboard' ) ) {
        echo SEO_OVERSIGHT_Dashboard::render_dashboard_shortcode();
    } else {
        echo '<div class="seo-alert seo-alert-danger">افزونه مکمل seo-oversight-core جهت نمایش داشبورد فعال نشده است.</div>';
    }
    ?>
</div>

<?php get_footer(); ?>
