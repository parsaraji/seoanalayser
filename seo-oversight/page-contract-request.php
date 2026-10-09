<?php
/**
 * Template Name: Form - Contract Request
 */

get_header();
$plan_id = absint( $_GET['plan'] ?? 0 );
$selected_plan = $plan_id && class_exists('SEO_OVERSIGHT_Service_Plans') ? SEO_OVERSIGHT_Service_Plans::get_plan( $plan_id ) : null;
$plans = class_exists('SEO_OVERSIGHT_Service_Plans') ? SEO_OVERSIGHT_Service_Plans::get_all_plans( true ) : array();
$msg = sanitize_text_field( $_GET['seo_msg'] ?? '' );
$num = sanitize_text_field( $_GET['num'] ?? '' );
?>

<div class="container section-padding">
    <div style="max-width:750px; margin:0 auto;">
        <h1>درخواست تنظیم و پذیرش قرارداد نظارت سئو</h1>
        <p class="justify-text" style="color:var(--muted-text); margin-bottom:2rem;">
            لطفاً اطلاعات حقیقی/حقوقی خود را جهت صدور پیش‌نویس قرارداد الکترونیکی نظارت مستقل سئو وارد نمایید.
        </p>

        <?php if ( $msg === 'contract_submitted' ) : ?>
            <div class="seo-alert seo-alert-success">
                <h4>پیش‌نویس قرارداد با موفقیت صادر شد!</h4>
                <p>شماره قرارداد: <strong dir="ltr"><?php echo esc_html( $num ); ?></strong></p>
                <p>جهت مشاهده، بررسی و پذیرش الکترونیکی قرارداد وارد <a href="<?php echo esc_url( home_url('/dashboard/?tab=contracts') ); ?>">داشبورد کاربری</a> خود شوید.</p>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="seo-form">
            <input type="hidden" name="action" value="seo_request_contract">
            <input type="text" name="website_hp" style="display:none !important;" tabindex="-1" autocomplete="off">
            <?php wp_nonce_field( 'seo_contract_request_action', 'seo_contract_request_nonce' ); ?>

            <div class="seo-form-group">
                <label>انتخاب پلن نظارتی <span class="required">*</span></label>
                <select name="plan_id" required>
                    <?php foreach ( $plans as $p ) : ?>
                        <option value="<?php echo esc_attr( $p->id ); ?>" <?php selected( $selected_plan ? $selected_plan->id : 0, $p->id ); ?>>
                            <?php echo esc_html( $p->name . ' — ' . number_format($p->monthly_price_toman) . ' تومان/ماهانه' ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="seo-form-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div class="seo-form-group">
                    <label>نام و نام خانوادگی طرف قرارداد <span class="required">*</span></label>
                    <input type="text" name="client_name" required placeholder="مثلاً: محمد حسینی">
                </div>

                <div class="seo-form-group">
                    <label>نام شرکت / سازمان (در صورت حقوقی بودن)</label>
                    <input type="text" name="client_company" placeholder="شرکت فلان (سهامی خاص)">
                </div>

                <div class="seo-form-group">
                    <label>کد ملی / شناسه ملی شرکت</label>
                    <input type="text" name="client_national_id" dir="ltr">
                </div>

                <div class="seo-form-group">
                    <label>شماره همراه <span class="required">*</span></label>
                    <input type="text" name="client_mobile" required dir="ltr" placeholder="۰۹۱۲۰۰۰۰۰۰۰">
                </div>
            </div>

            <div class="seo-form-group">
                <label>پست الکترونیکی (ایمیل)</label>
                <input type="email" name="client_email" dir="ltr" placeholder="client@example.com">
                <small>اطلاعات حساب کاربری و لینک قرارداد به این ایمیل ارسال می‌گردد.</small>
            </div>

            <div class="seo-form-group">
                <label>آدرس وب‌سایت موضوع قرارداد <span class="required">*</span></label>
                <input type="url" name="website_url" required dir="ltr" placeholder="https://example.com">
            </div>

            <div class="seo-form-group">
                <label>آدرس اقامتگاه / دفتر مرکزی</label>
                <textarea name="client_address" rows="2"></textarea>
            </div>

            <button type="submit" class="seo-btn seo-btn-primary" style="width:100%;">ثبت و صدور پیش‌نویس قرارداد</button>
        </form>
    </div>
</div>

<?php get_footer(); ?>
