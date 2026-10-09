<?php
get_header();
?>

<div class="container section-padding" style="text-align:center; min-height:50vh; display:flex; flex-direction:column; justify-content:center; align-items:center;">
    <h1 style="font-size:4rem; color:var(--primary-navy);">۴۰۴</h1>
    <h2>صفحه مورد نظر یافت نشد!</h2>
    <p style="color:var(--muted-text); max-width:500px; margin-bottom:2rem;">
        صفحه‌ای که به دنبال آن بودید انتقال یافته یا حذف شده است. می‌توانید از دکمه زیر جهت بازگشت به صفحه اصلی استفاده کنید.
    </p>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="seo-btn seo-btn-primary">&rarr; بازگشت به صفحه اصلی</a>
</div>

<?php get_footer(); ?>
