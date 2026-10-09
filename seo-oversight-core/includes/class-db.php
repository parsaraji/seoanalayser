<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_DB {
    const DB_VERSION = '1.0.0';

    public static function init_db() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $table_plans = $wpdb->prefix . 'seo_plans';
        $table_consultations = $wpdb->prefix . 'seo_consultations';
        $table_assessments = $wpdb->prefix . 'seo_assessments';
        $table_contracts = $wpdb->prefix . 'seo_contracts';
        $table_contract_versions = $wpdb->prefix . 'seo_contract_versions';
        $table_payments = $wpdb->prefix . 'seo_payments';
        $table_reports = $wpdb->prefix . 'seo_reports';
        $table_logs = $wpdb->prefix . 'seo_activity_logs';

        $sql = "
        CREATE TABLE $table_plans (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(191) NOT NULL,
            slug varchar(191) NOT NULL,
            short_description text NOT NULL,
            detailed_description longtext NOT NULL,
            monthly_price_toman bigint(20) UNSIGNED NOT NULL DEFAULT 0,
            one_time_fee_toman bigint(20) UNSIGNED NOT NULL DEFAULT 0,
            billing_interval varchar(50) NOT NULL DEFAULT 'monthly',
            included_features longtext NOT NULL,
            excluded_features longtext NOT NULL,
            max_monthly_scope varchar(255) NOT NULL DEFAULT '',
            report_frequency varchar(100) NOT NULL DEFAULT 'monthly',
            meeting_allowance varchar(100) NOT NULL DEFAULT '1 meeting/month',
            support_response varchar(100) NOT NULL DEFAULT '24h',
            is_active tinyint(1) NOT NULL DEFAULT 1,
            is_featured tinyint(1) NOT NULL DEFAULT 0,
            display_order int(11) NOT NULL DEFAULT 0,
            cta_label varchar(191) NOT NULL DEFAULT 'درخواست مشاوره',
            cta_destination varchar(255) NOT NULL DEFAULT '#consultation',
            contract_terms_addendum longtext DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;

        CREATE TABLE $table_consultations (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            reference_id varchar(64) NOT NULL,
            user_id bigint(20) UNSIGNED DEFAULT 0,
            full_name varchar(191) NOT NULL,
            business_name varchar(191) NOT NULL,
            website_url varchar(255) NOT NULL,
            mobile varchar(50) NOT NULL,
            email varchar(191) DEFAULT '',
            current_seo_setup varchar(100) NOT NULL,
            website_type varchar(100) NOT NULL,
            business_objective text NOT NULL,
            primary_concerns text NOT NULL,
            preferred_contact_method varchar(50) NOT NULL DEFAULT 'phone',
            preferred_time varchar(100) DEFAULT '',
            additional_notes longtext DEFAULT '',
            status varchar(50) NOT NULL DEFAULT 'new',
            admin_notes longtext DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY reference_id (reference_id)
        ) $charset_collate;

        CREATE TABLE $table_assessments (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            reference_id varchar(64) NOT NULL,
            user_id bigint(20) UNSIGNED DEFAULT 0,
            full_name varchar(191) NOT NULL,
            mobile varchar(50) NOT NULL,
            email varchar(191) DEFAULT '',
            website_url varchar(255) NOT NULL,
            business_type varchar(100) NOT NULL,
            target_market varchar(191) NOT NULL,
            key_landing_pages text DEFAULT '',
            primary_concerns text NOT NULL,
            current_seo_provider varchar(100) NOT NULL,
            analytics_context text DEFAULT '',
            assessment_type varchar(100) NOT NULL DEFAULT 'baseline',
            status varchar(50) NOT NULL DEFAULT 'new',
            priority varchar(50) NOT NULL DEFAULT 'medium',
            assigned_reviewer_id bigint(20) UNSIGNED DEFAULT 0,
            admin_notes longtext DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY reference_id (reference_id)
        ) $charset_collate;

        CREATE TABLE $table_contracts (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            contract_number varchar(64) NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            plan_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
            client_name varchar(191) NOT NULL,
            client_company varchar(191) DEFAULT '',
            client_national_id varchar(50) DEFAULT '',
            client_address text DEFAULT '',
            client_mobile varchar(50) NOT NULL,
            client_email varchar(191) DEFAULT '',
            website_url varchar(255) NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'draft',
            monthly_fee_toman bigint(20) UNSIGNED NOT NULL,
            billing_interval varchar(50) NOT NULL DEFAULT 'monthly',
            start_date date DEFAULT NULL,
            end_date date DEFAULT NULL,
            current_version_id bigint(20) UNSIGNED DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY contract_number (contract_number)
        ) $charset_collate;

        CREATE TABLE $table_contract_versions (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            contract_id bigint(20) UNSIGNED NOT NULL,
            version_number int(11) NOT NULL DEFAULT 1,
            contract_body longtext NOT NULL,
            terms_snapshot longtext NOT NULL,
            created_by bigint(20) UNSIGNED NOT NULL,
            accepted_at datetime DEFAULT NULL,
            accepted_by_user_id bigint(20) UNSIGNED DEFAULT 0,
            acceptance_ip varchar(100) DEFAULT '',
            acceptance_user_agent text DEFAULT '',
            is_locked tinyint(1) NOT NULL DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;

        CREATE TABLE $table_payments (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            payment_reference varchar(64) NOT NULL,
            contract_id bigint(20) UNSIGNED NOT NULL,
            user_id bigint(20) UNSIGNED NOT NULL,
            amount_toman bigint(20) UNSIGNED NOT NULL,
            payer_name varchar(191) NOT NULL,
            transfer_date date NOT NULL,
            transaction_ref varchar(100) DEFAULT '',
            receipt_file_path text DEFAULT '',
            receipt_file_url text DEFAULT '',
            status varchar(50) NOT NULL DEFAULT 'awaiting_payment',
            admin_notes text DEFAULT '',
            verified_by bigint(20) UNSIGNED DEFAULT 0,
            verified_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY payment_reference (payment_reference)
        ) $charset_collate;

        CREATE TABLE $table_reports (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id bigint(20) UNSIGNED NOT NULL,
            contract_id bigint(20) UNSIGNED NOT NULL DEFAULT 0,
            title varchar(255) NOT NULL,
            reporting_period varchar(100) NOT NULL,
            executive_summary longtext NOT NULL,
            findings_json longtext DEFAULT '',
            attachment_file_path text DEFAULT '',
            attachment_file_url text DEFAULT '',
            status varchar(50) NOT NULL DEFAULT 'published',
            internal_notes text DEFAULT '',
            issued_date date NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;

        CREATE TABLE $table_logs (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            object_type varchar(50) NOT NULL,
            object_id bigint(20) UNSIGNED NOT NULL,
            action varchar(100) NOT NULL,
            performed_by bigint(20) UNSIGNED NOT NULL,
            details longtext DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;
        ";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        update_option( 'seo_oversight_db_version', self::DB_VERSION );

        self::insert_default_plans();
        self::create_default_pages();
    }

    public static function create_default_pages() {
        // Parent Services Page
        $services_parent_id = 0;
        $existing_services = get_page_by_path( 'services' );
        if ( ! $existing_services ) {
            $services_parent_id = wp_insert_post( array(
                'post_title'   => 'خدمات نظارت سئو',
                'post_name'    => 'services',
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => 'پلتفرم نظارت مستقل سئو خدمات خود را در قالب ۶ محور تخصصی ارائه می‌دهد.'
            ) );
        } else {
            $services_parent_id = $existing_services->ID;
        }

        $service_children = array(
            'technical-seo-oversight' => array(
                'title'   => 'نظارت بر سئوی فنی',
                'content' => 'خدمات پایش و ارزیابی تخصصی سئوی فنی شامل تحلیل کدهای کانونیکال، ساختار داده‌ها (Schema)، بودجه خزش، نحوه نمایه شدن صفحات و برطرف‌سازی خطاهای سرچ کنسول.'
            ),
            'onpage-content-review' => array(
                'title'   => 'بررسی سئوی داخلی و محتوا',
                'content' => 'ارزیابی کیفیت محتوایی و ساختاری وب‌سایت، هم‌راستایی با قصد کاربران (Search Intent)، پایش لینک‌سازی داخلی و جلوگیری از هم‌نوع‌خواری کلمات کلیدی (Cannibalization).'
            ),
            'performance-monitoring' => array(
                'title'   => 'تحلیل عملکرد ارگانیک',
                'content' => 'پایش مستمر نوسانات ورودی ارگانیک، آنالیز نرخ تبدیل، تحلیل سهم بازار در عبارت‌های کلیدی و پایش شاخص‌های حیاتی وب (Core Web Vitals).'
            ),
            'offpage-review' => array(
                'title'   => 'ارزیابی سئوی خارجی',
                'content' => 'سنجش سلامت پروفایل بک‌لینک، پایش آنکورتکست‌ها، شناسایی لینک‌های اسپم و مخرب و ارزیابی ریسک‌های جریمه و افت توسط الگوریتم‌های گوگل.'
            ),
            'task-monitoring' => array(
                'title'   => 'پیگیری وظایف و اصلاحات سئو',
                'content' => 'ردیابی زمان‌بندی اجرای توصیه‌های ناظر توسط مجری سئو یا تیم توسعه، صحت‌سنجی نهایی کیفیت اصلاحات و اطمینان از رفع پایدار مشکلات.'
            ),
        );

        foreach ( $service_children as $slug => $info ) {
            $existing_child = get_page_by_path( 'services/' . $slug );
            if ( ! $existing_child ) {
                wp_insert_post( array(
                    'post_title'   => $info['title'],
                    'post_name'    => $slug,
                    'post_parent'  => $services_parent_id,
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_content' => $info['content'],
                ) );
            }
        }

        $pages = array(
            'consultation' => array(
                'title' => 'درخواست مشاوره',
                'template' => 'page-consultation.php',
            ),
            'assessment' => array(
                'title' => 'درخواست ارزیابی سئو',
                'template' => 'page-assessment.php',
            ),
            'contract-request' => array(
                'title' => 'درخواست قرارداد',
                'template' => 'page-contract-request.php',
            ),
            'pricing' => array(
                'title' => 'تعرفه‌ها و پلن‌ها',
                'template' => 'page-pricing.php',
            ),
            'dashboard' => array(
                'title' => 'داشبورد کاربری',
                'template' => 'page-dashboard.php',
            ),
            'blog' => array(
                'title' => 'وبلاگ تحلیلی',
                'template' => 'page-blog.php',
            ),
            'services' => array(
                'title' => 'خدمات نظارت سئو',
                'template' => 'page.php',
                'content' => 'پلتفرم نظارت مستقل سئو خدمات خود را در قالب ۶ محور تخصصی سئوی فنی، سئوی داخلی، تحلیل عملکرد ارگانیک، ارزیابی سئوی خارجی، پیگیری اصلاحات و گزارش‌های مدیریتی ارائه می‌دهد.'
            ),
            'about' => array(
                'title' => 'درباره نظارت مستقل سئو',
                'template' => 'page.php',
                'content' => 'ما به عنوان مشاور و ناظر بی‌پرفورمنس و مستقل، بر کیفیت عملکرد مجریان و آژانس‌های سئو نظارت می‌کنیم تا سرمایه‌گذاری کسب‌وکارها کاملاً شفاف و اثرگذار باشد.'
            ),
            'how-it-works' => array(
                'title' => 'چگونگی عملکرد پایش سئو',
                'template' => 'page.php',
                'content' => 'فرآیند پایش در ۴ مرحله شامل مشاوره و اعطای دسترسی سطح Viewer، ارزیابی وضعیت مبنا (Baseline)، پیگیری هفتگی وظایف و تحویل گزارش‌های ماهانه صورت می‌پذیرد.'
            ),
            'contact' => array(
                'title' => 'تماس باما',
                'template' => 'page.php',
                'content' => 'جهت برقراری ارتباط با کارشناسان ناظر می‌توانید با شماره ۰۲۱-۸۸۰۰۰۰۰۰ تماس گرفته یا فرم مشاوره را تکمیل نمایید.'
            ),
            'privacy-policy' => array(
                'title' => 'حریم خصوصی و حفاظت از داده‌ها',
                'template' => 'page.php',
                'content' => 'تمام اسناد، داده‌های سرچ کنسول، گزارش‌ها و اطلاعات مالی مشتریان نزد پلتفرم نظارت مستقل سئو کاملاً محرمانه تلقی می‌گردد.'
            ),
            'terms' => array(
                'title' => 'شرایط عمومی خدمات',
                'template' => 'page.php',
                'content' => 'ارائه خدمات نظارتی منوط به ثبت درخواست، پذیرش الکترونیکی قرارداد و تایید فیش واریز وجه ماهانه می‌باشد.'
            ),
            'contract-terms' => array(
                'title' => 'نمونه مفاد حقوقی قرارداد',
                'template' => 'page.php',
                'content' => 'این صفحه شامل پیش‌نمایش مواد ۱ تا ۱۶ قرارداد ارائه خدمات نظارت مستقل سئو می‌باشد.'
            ),
            'payment-instructions' => array(
                'title' => 'راهنمای واریز کارت به کارت',
                'template' => 'page.php',
                'content' => 'پرداخت هزینه‌های نظارت صرفاً از طریق واریز کارت به کارت به شماره کارت اعلام‌شده در داشبورد و ثبت فیش واریزی انجام می‌شود.'
            ),
            'faq' => array(
                'title' => 'پرسش‌های متداول',
                'template' => 'page.php',
                'content' => 'پاسخ به کلیه پرسش‌های کلیدی درباره نحوه نظارت، عدم دخالت مستقیم در کدنویسی، حفظ محرمانگی و نحوه پرداخت.'
            ),
        );

        foreach ( $pages as $slug => $page_info ) {
            $existing = get_page_by_path( $slug );
            if ( ! $existing ) {
                $page_id = wp_insert_post( array(
                    'post_title'     => $page_info['title'],
                    'post_name'      => $slug,
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                    'post_content'   => isset( $page_info['content'] ) ? $page_info['content'] : '',
                ) );

                if ( $page_id && ! is_wp_error( $page_id ) && ! empty( $page_info['template'] ) ) {
                    update_post_meta( $page_id, '_wp_page_template', $page_info['template'] );
                    if ( $slug === 'dashboard' ) {
                        update_option( 'seo_oversight_dashboard_page_id', $page_id );
                    }
                }
            } else {
                if ( ! empty( $page_info['template'] ) ) {
                    update_post_meta( $existing->ID, '_wp_page_template', $page_info['template'] );
                }
                if ( $slug === 'dashboard' ) {
                    update_option( 'seo_oversight_dashboard_page_id', $existing->ID );
                }
            }
        }
    }

    public static function check_version() {
        if ( get_option( 'seo_oversight_db_version' ) !== self::DB_VERSION ) {
            self::init_db();
        }
    }

    public static function insert_default_plans() {
        global $wpdb;
        $table_plans = $wpdb->prefix . 'seo_plans';

        $count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_plans" );
        if ( $count > 0 ) {
            return;
        }

        $defaults = array(
            array(
                'name' => 'پایش پایه (Basic Oversight)',
                'slug' => 'basic-oversight',
                'short_description' => 'مناسب برای کسب‌وکارهای کوچک با تیم سئوی تک‌نفره یا فریلنسر',
                'detailed_description' => 'بررسی ماهانه سلامت فنی، نمایه‌سازی، خطاهای سرچ کنسول و نظارت بر رعایت استانداردهای محتوایی.',
                'monthly_price_toman' => 8500000,
                'one_time_fee_toman' => 0,
                'billing_interval' => 'monthly',
                'included_features' => "نظارت بر خطاهای فنی سرچ کنسول\nبررسی ماهانه لایو بودن صفحه‌ها و لینک‌های شکسته\nارزیابی اولیه کیفیت محتوا و هم‌راستایی با قصد کاربر\nارسال ۱ گزارش جامع تحلیلی در ماه\n۱ جلسه آنلاین بررسی گزارش (۴۵ دقیقه)",
                'excluded_features' => "اجرای مستقیم تغییرات فنی کد یا قالب\nتولید محتوا یا انتشار مقاله\nخرید بک‌لینک یا اجرای کمپین Off-Page\nتضمین رتبه یا ترافیک مشخص",
                'max_monthly_scope' => 'تا ۵۰۰ صفحه کلیدی',
                'report_frequency' => 'ماهانه',
                'meeting_allowance' => '۱ جلسه آنلاین در ماه',
                'support_response' => '۴۸ ساعت کاری',
                'is_active' => 1,
                'is_featured' => 0,
                'display_order' => 1,
                'cta_label' => 'درخواست مشاوره و تنظیم قرارداد',
                'cta_destination' => '#contract-request',
            ),
            array(
                'name' => 'نظارت تخصصی (Professional Oversight)',
                'slug' => 'pro-oversight',
                'short_description' => 'مناسب برای شرکت‌ها و فروشگاه‌های آنلاین دارای تیم سئوی درونی یا آژانس طرف قرارداد',
                'detailed_description' => 'نظارت همه‌جانبه بر سئوی فنی، سئوی داخلی، کیفیت محتوا، پروفایل بک‌لینک‌ها و پیگیری هفتگی وظایف ارجاع‌شده به مجری.',
                'monthly_price_toman' => 16500000,
                'one_time_fee_toman' => 0,
                'billing_interval' => 'monthly',
                'included_features' => "تحلیل عمیق سئوی فنی (Technical SEO Audit)\nپایش کیفیت محتوا و جلوگیری از Cannibalization\nارزیابی ریسک‌های Off-Page و بک‌لینک‌ها\nپیگیری هفتگی انجام اصلاحات توسط تیم مجری\n۲ گزارش مدیریت در ماه + ۱ جلسه بررسی و هم‌افزایی\nبررسی عملکرد فنی و سرعت (Core Web Vitals)",
                'excluded_features' => "طراحی و توسعه وب‌سایت\nتولید محتوا و مدیریت گرافیک\nتضمین رتبه اول گوگل",
                'max_monthly_scope' => 'تا ۳۰۰۰ صفحه',
                'report_frequency' => 'دو هفته یک‌بار',
                'meeting_allowance' => '۲ جلسه آنلاین در ماه',
                'support_response' => '۲۴ ساعت کاری',
                'is_active' => 1,
                'is_featured' => 1,
                'display_order' => 2,
                'cta_label' => 'انتخاب پلن تخصصی',
                'cta_destination' => '#contract-request',
            ),
            array(
                'name' => 'نظارت سازمانی (Enterprise Oversight)',
                'slug' => 'enterprise-oversight',
                'short_description' => 'ویژه پرتال‌های بزرگ، وب‌سایت‌های چندمنظوره و کسب‌وکارهای پیشرو',
                'detailed_description' => 'نظارت استراتژیک، پایش آنلاین و مداوم، ارزیابی امنیت سئو و ساختار معماری، به همراه پشتیبانی اختصاصی برای مدیران عالی.',
                'monthly_price_toman' => 29000000,
                'one_time_fee_toman' => 5000000,
                'billing_interval' => 'monthly',
                'included_features' => "تمام امکانات پلن تخصصی\nنظارت سفارشی بر معماری اطلاعات و Migration\nحضور ناظر در جلسات استراتژیک با آژانس یا تیم سئو\nگزارش‌های هفتگی ناهنجاری‌ها و افت‌های ناگهانی\nپشتیبانی مستقیم و خط اختصاصی مشاوره به مدیرعامل\nجلسات حضوری/آنلاین نامحدود (طبق توافق)",
                'excluded_features' => "اجرای مستقیم کدنویسی یا تولید محتوا",
                'max_monthly_scope' => 'بدون محدودیت تعداد صفحه',
                'report_frequency' => 'هفتگی و ماهانه',
                'meeting_allowance' => 'تا ۴ جلسه در ماه',
                'support_response' => '۱۲ ساعت کاری',
                'is_active' => 1,
                'is_featured' => 0,
                'display_order' => 3,
                'cta_label' => 'درخواست جلسات سازمانی',
                'cta_destination' => '#contract-request',
            )
        );

        foreach ( $defaults as $plan ) {
            $wpdb->insert( $table_plans, $plan );
        }
    }
}
