<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Theme_Options {
    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_theme_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
    }

    public static function add_theme_menu() {
        add_theme_page(
            'تنظیمات قالب نظارت سئو',
            'تنظیمات قالب',
            'edit_theme_options',
            'seo-oversight-theme-settings',
            array( __CLASS__, 'render_settings_page' )
        );
    }

    public static function register_settings() {
        register_setting( 'seo_oversight_theme_options_group', 'seo_oversight_theme_options' );
    }

    public static function get_options() {
        $defaults = array(
            'announcement_active' => 1,
            'announcement_text' => 'سامانه پایش و ارزیابی بی‌پرفورمنس و مستقل سئو ویژه کسب‌وکارهای ایرانی',
            'announcement_link' => '/consultation/',
            'hero_title' => 'نظارت مستقل بر عملکرد سئو؛ با شواهد، نه حدس',
            'hero_desc' => 'اگر برای سئوی سایت خود هزینه می‌کنید، بتوانید با دیدی مستقل بدانید چه مشکلاتی وجود دارد، چه اقداماتی باید انجام شود و اصلاحات تا چه مرحله‌ای پیش رفته‌اند.',
            'footer_about' => 'پلتفرم تخصصی ارائه خدمات نظارت مستقل سئو، حسابرسی کیفیت فنی و آنالیز بی‌پرفورمنس سئو جهت شفاف‌سازی و حفاظت از سرمایه‌گذاری کسب‌وکارها.',
            'copyright_text' => 'تمامی حقوق مادی و معنوی این پلتفرم محفوظ است.',
            'contact_phone' => '۰۲۱-۸۸۰۰۰۰۰۰',
            'contact_email' => 'info@seo-oversight.ir',
            'contact_address' => 'تهران، خیابان ولیعصر، بالاتر از پارک وی، پلاک ۱۰۰',
            'site_title_format' => '%title% | پلتفرم نظارت مستقل سئو',
            'meta_desc_default' => 'ارائه خدمات تخصصی پایش، حسابرسی و نظارت مستقل بر عملکرد سئوی وب‌سایت‌ها جهت اطمینان از سلامت فنی و کیفیت اقدامات مجری سئو.'
        );
        $saved = get_option( 'seo_oversight_theme_options', array() );
        return wp_parse_args( $saved, $defaults );
    }

    public static function render_settings_page() {
        $options = self::get_options();
        ?>
        <div class="wrap" dir="rtl">
            <h1>تنظیمات اختصاصی قالب SEO Oversight</h1>
            <hr>
            <form method="post" action="options.php" style="background:#fff; padding:25px; border:1px solid #ccc; max-width:800px; margin-top:20px;">
                <?php
                settings_fields( 'seo_oversight_theme_options_group' );
                ?>

                <h2>۱. بنر اطلاعیه بالای سایت (Announcement Bar)</h2>
                <p><label><input type="checkbox" name="seo_oversight_theme_options[announcement_active]" value="1" <?php checked( $options['announcement_active'], 1 ); ?>> بنر بالای هدر فعال باشد</label></p>
                <p><label><strong>متن بنر:</strong></label><br>
                <input type="text" name="seo_oversight_theme_options[announcement_text]" value="<?php echo esc_attr( $options['announcement_text'] ); ?>" style="width:100%;"></p>
                <p><label><strong>لینک بنر:</strong></label><br>
                <input type="text" name="seo_oversight_theme_options[announcement_link]" value="<?php echo esc_attr( $options['announcement_link'] ); ?>" style="width:100%;"></p>

                <hr>
                <h2>۲. بخش هیرو صفحه اصلی (Hero Section)</h2>
                <p><label><strong>عنوان هیرو (H1):</strong></label><br>
                <input type="text" name="seo_oversight_theme_options[hero_title]" value="<?php echo esc_attr( $options['hero_title'] ); ?>" style="width:100%;"></p>
                <p><label><strong>توضیحات هیرو:</strong></label><br>
                <textarea name="seo_oversight_theme_options[hero_desc]" rows="3" style="width:100%;"><?php echo esc_textarea( $options['hero_desc'] ); ?></textarea></p>

                <hr>
                <h2>۳. تنظیمات ارتباطی و فوتر</h2>
                <p><label><strong>تلفن تماس:</strong></label><br>
                <input type="text" name="seo_oversight_theme_options[contact_phone]" value="<?php echo esc_attr( $options['contact_phone'] ); ?>" style="width:100%;"></p>
                <p><label><strong>ایمیل تماس:</strong></label><br>
                <input type="text" name="seo_oversight_theme_options[contact_email]" value="<?php echo esc_attr( $options['contact_email'] ); ?>" style="width:100%;"></p>
                <p><label><strong>آدرس:</strong></label><br>
                <input type="text" name="seo_oversight_theme_options[contact_address]" value="<?php echo esc_attr( $options['contact_address'] ); ?>" style="width:100%;"></p>
                <p><label><strong>توضیحات فوتر:</strong></label><br>
                <textarea name="seo_oversight_theme_options[footer_about]" rows="3" style="width:100%;"><?php echo esc_textarea( $options['footer_about'] ); ?></textarea></p>
                <p><label><strong>متن کپی‌رایت:</strong></label><br>
                <input type="text" name="seo_oversight_theme_options[copyright_text]" value="<?php echo esc_attr( $options['copyright_text'] ); ?>" style="width:100%;"></p>

                <hr>
                <h2>۴. تنظیمات سئو و متادیتا</h2>
                <p><label><strong>توضیحات متای پیش‌فرض (Meta Description):</strong></label><br>
                <textarea name="seo_oversight_theme_options[meta_desc_default]" rows="2" style="width:100%;"><?php echo esc_textarea( $options['meta_desc_default'] ); ?></textarea></p>

                <br>
                <button type="submit" class="button button-primary button-large">ذخیره تمامی تنظیمات قالب</button>
            </form>
        </div>
        <?php
    }
}
SEO_OVERSIGHT_Theme_Options::init();
