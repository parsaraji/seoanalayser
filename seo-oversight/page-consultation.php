<?php
/**
 * Template Name: Form - Consultation Request
 */

get_header();
$msg = sanitize_text_field( $_GET['seo_msg'] ?? '' );
$ref = sanitize_text_field( $_GET['ref'] ?? '' );
?>

<div class="container section-padding">
    <div style="max-width:700px; margin:0 auto;">
        <h1>درخواست مشاوره اختصاصی نظارت سئو</h1>
        <p class="justify-text" style="color:var(--muted-text); margin-bottom:2rem;">
            لطفاً فرم زیر را جهت بررسی اولیه اطلاعات کسب‌وکار و وب‌سایت خود تکمیل فرمایید. کارشناسان ناظر ما جهت هماهنگی جلسه با شما تماس خواهند گرفت.
        </p>

        <?php if ( $msg === 'success' ) : ?>
            <div class="seo-alert seo-alert-success">
                <h4>درخواست مشاوره شما با موفقیت ثبت شد!</h4>
                <p>کد پیگیری درخواست شما: <strong dir="ltr"><?php echo esc_html( $ref ); ?></strong></p>
                <p>همکاران ما به زودی با شماره همراه ثبت‌شده تماس خواهند گرفت.</p>
            </div>
        <?php elseif ( $msg === 'missing_fields' ) : ?>
            <div class="seo-alert seo-alert-danger">لطفاً تمامی فیلدهای ضروری ستاره‌دار را تکمیل نمایید.</div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="seo-form">
            <input type="hidden" name="action" value="seo_submit_consultation">
            <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">
            <?php wp_nonce_field( 'seo_submit_consultation_action', 'seo_consultation_nonce' ); ?>

            <div class="seo-form-group">
                <label>نام و نام خانوادگی <span class="required">*</span></label>
                <input type="text" name="full_name" required placeholder="مثلاً: علی رضایی">
            </div>

            <div class="seo-form-group">
                <label>نام شرکت یا کسب‌وکار</label>
                <input type="text" name="business_name" placeholder="نام برند یا سازمان">
            </div>

            <div class="seo-form-group">
                <label>آدرس وب‌سایت <span class="required">*</span></label>
                <input type="url" name="website_url" required dir="ltr" placeholder="https://example.com">
            </div>

            <div class="seo-form-group">
                <label>شماره همراه <span class="required">*</span></label>
                <input type="text" name="mobile" required dir="ltr" placeholder="۰۹۱۲۰۰۰۰۰۰۰">
            </div>

            <div class="seo-form-group">
                <label>ایمیل (اختیاری)</label>
                <input type="email" name="email" dir="ltr" placeholder="info@example.com">
            </div>

            <div class="seo-form-group">
                <label>ساختار فعلی سئوی وب‌سایت شما چگونه است؟</label>
                <select name="current_seo_setup">
                    <option value="in_house">تیم سئوی اختصاصی درونی داریم</option>
                    <option value="agency">با آژانس سئو قرارداد داریم</option>
                    <option value="freelancer">با کارشناس فریلنسر همکاری می‌کنیم</option>
                    <option value="none">در حال حاضر مجری سئو نداریم</option>
                </select>
            </div>

            <div class="seo-form-group">
                <label>نوع وب‌سایت</label>
                <select name="website_type">
                    <option value="ecommerce">فروشگاه آنلاین (E-commerce)</option>
                    <option value="corporate">شرکتی / خدماتی</option>
                    <option value="portal">پرتال خبری / محتوایی</option>
                    <option value="startup">استارتاپ / اپلیکیشن</option>
                </select>
            </div>

            <div class="seo-form-group">
                <label>هدف اصلی شما از درخواست نظارت مستقل سئو چیست؟</label>
                <textarea name="business_objective" rows="3" placeholder="مثلاً: حصول اطمینان از کیفیت اقدامات مجری، حل خطاهای سرچ کنسول و..."></textarea>
            </div>

            <div class="seo-form-group">
                <label>مهم‌ترین دغدغه‌ها یا چالش‌های فعلی سئوی سایت</label>
                <textarea name="primary_concerns" rows="3"></textarea>
            </div>

            <div class="seo-form-group">
                <label><input type="checkbox" name="privacy_consent" value="1" required> با <a href="<?php echo esc_url( home_url('/privacy-policy/') ); ?>" target="_blank">بیانیه حریم خصوصی</a> موافقت دارم.</label>
            </div>

            <button type="submit" class="seo-btn seo-btn-primary" style="width:100%;">ثبت درخواست مشاوره</button>
        </form>
    </div>
</div>

<?php get_footer(); ?>
