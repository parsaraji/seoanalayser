<?php
/**
 * Template Name: Form - Assessment Request
 */

get_header();
$msg = sanitize_text_field( $_GET['seo_msg'] ?? '' );
$ref = sanitize_text_field( $_GET['ref'] ?? '' );
?>

<div class="container section-padding">
    <div style="max-width:700px; margin:0 auto;">
        <h1>درخواست ارزیابی اولیه و پایش فنی سئو</h1>
        <p class="justify-text" style="color:var(--muted-text); margin-bottom:2rem;">
            با تکمیل این فرم، وب‌سایت شما جهت بررسی وضعیت سلامت فنی، خطاهای نمایه‌سازی و کیفیت کلی سئو در صف ارزیابی اولیه ناظر قرار خواهد گرفت.
        </p>

        <?php if ( $msg === 'success' ) : ?>
            <div class="seo-alert seo-alert-success">
                <h4>درخواست ارزیابی سئو با موفقیت ثبت شد!</h4>
                <p>کد پیگیری ارزیابی شما: <strong dir="ltr"><?php echo esc_html( $ref ); ?></strong></p>
            </div>
        <?php elseif ( $msg === 'missing_fields' ) : ?>
            <div class="seo-alert seo-alert-danger">لطفاً تمامی فیلدهای ضروری را تکمیل فرمایید.</div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="seo-form">
            <input type="hidden" name="action" value="seo_submit_assessment">
            <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">
            <?php wp_nonce_field( 'seo_submit_assessment_action', 'seo_assessment_nonce' ); ?>

            <div class="seo-form-group">
                <label>نام و نام خانوادگی <span class="required">*</span></label>
                <input type="text" name="full_name" required>
            </div>

            <div class="seo-form-group">
                <label>شماره همراه جهت هماهنگی <span class="required">*</span></label>
                <input type="text" name="mobile" required dir="ltr">
            </div>

            <div class="seo-form-group">
                <label>آدرس وب‌سایت <span class="required">*</span></label>
                <input type="url" name="website_url" required dir="ltr" placeholder="https://example.com">
            </div>

            <div class="seo-form-group">
                <label>نوع ارزیابی درخواستی</label>
                <select name="assessment_type">
                    <option value="baseline">ارزیابی جامع مبنا (Baseline SEO Audit)</option>
                    <option value="technical">ارزیابی تخصصی سئوی فنی (Technical Audit)</option>
                    <option value="content_cannibalization">ارزیابی محتوا و Cannibalization</option>
                    <option value="offpage_risk">ارزیابی ریسک بک‌لینک و Off-Page</option>
                </select>
            </div>

            <div class="seo-form-group">
                <label>نوع فعالیت کسب‌وکار</label>
                <input type="text" name="business_type">
            </div>

            <div class="seo-form-group">
                <label>مهم‌ترین صفحات لندینگ یا کلمات کلیدی (اختیاری)</label>
                <textarea name="key_landing_pages" rows="3"></textarea>
            </div>

            <div class="seo-form-group">
                <label>مهم‌ترین دغدغه‌ها و نگرانی‌های سئوی سایت</label>
                <textarea name="primary_concerns" rows="3"></textarea>
            </div>

            <div style="background:#fffbe6; padding:12px; border-radius:6px; margin:15px 0; font-size:0.875rem; color:#8c6c00;">
                <strong>تذکر امنیتی مهم:</strong> به هیچ عنوان کلمه عبور، کلیدهای API یا دسترسی‌های مستقیم به وب‌سایت را در این فرم ارسال نکنید. دسترسی به ابزارهای تحلیلی بعداً تنها از طریق سیستم‌های دسترسی رسمی (Viewer) انجام خواهد شد.
            </div>

            <div class="seo-form-group">
                <label><input type="checkbox" name="privacy_consent" value="1" required> با <a href="<?php echo esc_url( home_url('/privacy-policy/') ); ?>" target="_blank">بیانیه حریم خصوصی</a> موافقت دارم.</label>
            </div>

            <button type="submit" class="seo-btn seo-btn-primary" style="width:100%;">ثبت درخواست ارزیابی سئو</button>
        </form>
    </div>
</div>

<?php get_footer(); ?>
