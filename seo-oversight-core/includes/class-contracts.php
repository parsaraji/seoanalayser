<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Contracts {
    public static function init() {
        add_action( 'admin_post_seo_request_contract', array( __CLASS__, 'handle_contract_request' ) );
        add_action( 'admin_post_nopriv_seo_request_contract', array( __CLASS__, 'handle_contract_request' ) );

        add_action( 'admin_post_seo_accept_contract', array( __CLASS__, 'handle_contract_acceptance' ) );
        add_action( 'admin_post_seo_request_contract_revision', array( __CLASS__, 'handle_contract_revision_request' ) );
    }

    public static function generate_contract_number() {
        return 'CTR-' . date('Ym') . '-' . strtoupper( wp_generate_password( 5, false, false ) );
    }

    public static function handle_contract_request() {
        if ( ! isset( $_POST['seo_contract_request_nonce'] ) || ! wp_verify_nonce( $_POST['seo_contract_request_nonce'], 'seo_contract_request_action' ) ) {
            wp_die( 'ارزیابی اعتبار امنیتی (Nonce) ناموفق بود.', 'خطای امنیتی', array( 'response' => 403 ) );
        }

        if ( ! empty( $_POST['website_hp'] ) ) {
            wp_die( 'درخواست غیرمجاز.', 'خطای هرزنامه', array( 'response' => 400 ) );
        }

        $plan_id = absint( $_POST['plan_id'] ?? 0 );
        $client_name = sanitize_text_field( $_POST['client_name'] ?? '' );
        $client_company = sanitize_text_field( $_POST['client_company'] ?? '' );
        $client_national_id = sanitize_text_field( $_POST['client_national_id'] ?? '' );
        $client_address = sanitize_textarea_field( $_POST['client_address'] ?? '' );
        $client_mobile = sanitize_text_field( $_POST['client_mobile'] ?? '' );
        $client_email = sanitize_email( $_POST['client_email'] ?? '' );
        $website_url = esc_url_raw( $_POST['website_url'] ?? '' );

        $redirect_back = wp_get_referer() ? wp_get_referer() : home_url( '/contract-request/' );

        if ( empty( $client_name ) || empty( $client_mobile ) || empty( $website_url ) || ! $plan_id ) {
            wp_safe_redirect( add_query_arg( 'seo_msg', 'missing_fields', $redirect_back ) );
            exit;
        }

        $plan = SEO_OVERSIGHT_Service_Plans::get_plan( $plan_id );
        if ( ! $plan ) {
            wp_safe_redirect( add_query_arg( 'seo_msg', 'invalid_plan', $redirect_back ) );
            exit;
        }

        $user_id = get_current_user_id();

        if ( ! $user_id && ! empty( $client_email ) ) {
            $existing_user = get_user_by( 'email', $client_email );
            if ( $existing_user ) {
                // Keep user_id = 0 for guest submissions with an existing email to prevent unauthenticated account spoofing.
                $user_id = 0;
            } else {
                $username = 'client_' . sanitize_title( $client_mobile );
                if ( username_exists( $username ) ) {
                    $username .= '_' . wp_generate_password( 4, false, false );
                }
                $random_pass = wp_generate_password( 12, true );
                $created_user_id = wp_create_user( $username, $random_pass, $client_email );
                if ( ! is_wp_error( $created_user_id ) ) {
                    $user_id = $created_user_id;
                    $user_obj = new WP_User( $user_id );
                    $user_obj->set_role( 'seo_client' );
                    update_user_meta( $user_id, 'display_name', $client_name );
                    update_user_meta( $user_id, 'billing_phone', $client_mobile );
                }
            }
        }

        global $wpdb;
        $table_contracts = $wpdb->prefix . 'seo_contracts';
        $contract_num = self::generate_contract_number();

        $wpdb->insert(
            $table_contracts,
            array(
                'contract_number' => $contract_num,
                'user_id' => $user_id ? $user_id : 0,
                'plan_id' => $plan_id,
                'client_name' => $client_name,
                'client_company' => $client_company,
                'client_national_id' => $client_national_id,
                'client_address' => $client_address,
                'client_mobile' => $client_mobile,
                'client_email' => $client_email,
                'website_url' => $website_url,
                'status' => 'submitted',
                'monthly_fee_toman' => $plan->monthly_price_toman,
                'billing_interval' => $plan->billing_interval
            )
        );

        $contract_id = $wpdb->insert_id;

        // Draft initial contract version using template
        self::create_contract_version( $contract_id, $user_id, $plan, array(
            'client_name' => $client_name,
            'client_company' => $client_company,
            'client_national_id' => $client_national_id,
            'client_address' => $client_address,
            'client_mobile' => $client_mobile,
            'client_email' => $client_email,
            'website_url' => $website_url,
        ) );

        SEO_OVERSIGHT_Notifications::send_admin_new_contract_request( $contract_num, $client_name, $website_url );

        wp_safe_redirect( add_query_arg( array( 'seo_msg' => 'contract_submitted', 'num' => $contract_num ), $redirect_back ) );
        exit;
    }

    public static function create_contract_version( $contract_id, $created_by, $plan, $client_data ) {
        global $wpdb;
        $table_versions = $wpdb->prefix . 'seo_contract_versions';
        $table_contracts = $wpdb->prefix . 'seo_contracts';

        $latest_version_num = $wpdb->get_var( $wpdb->prepare( "SELECT MAX(version_number) FROM $table_versions WHERE contract_id = %d", $contract_id ) );
        $new_version_num = $latest_version_num ? intval( $latest_version_num ) + 1 : 1;

        $terms_snapshot = array(
            'plan_name' => $plan->name,
            'monthly_price_toman' => $plan->monthly_price_toman,
            'billing_interval' => $plan->billing_interval,
            'included_features' => $plan->included_features,
            'excluded_features' => $plan->excluded_features,
            'addendum' => $plan->contract_terms_addendum,
            'client_info' => $client_data
        );

        $contract_body = self::render_default_persian_contract_text( $plan, $client_data );

        $wpdb->insert(
            $table_versions,
            array(
                'contract_id' => $contract_id,
                'version_number' => $new_version_num,
                'contract_body' => wp_kses_post( $contract_body ),
                'terms_snapshot' => wp_json_encode( $terms_snapshot, JSON_UNESCAPED_UNICODE ),
                'created_by' => $created_by ? $created_by : get_current_user_id(),
                'is_locked' => 0
            )
        );

        $version_id = $wpdb->insert_id;

        $wpdb->update(
            $table_contracts,
            array( 'current_version_id' => $version_id ),
            array( 'id' => $contract_id )
        );

        return $version_id;
    }

    public static function handle_contract_acceptance() {
        if ( ! is_user_logged_in() ) {
            wp_die( 'لطفاً وارد حساب کاربری خود شوید.', 'دسترسی غیرمجاز', array( 'response' => 403 ) );
        }

        if ( ! isset( $_POST['seo_contract_accept_nonce'] ) || ! wp_verify_nonce( $_POST['seo_contract_accept_nonce'], 'seo_accept_contract_action' ) ) {
            wp_die( 'اعتبارسنجی امنیتی ناموفق بود.', 'خطای امنیتی', array( 'response' => 403 ) );
        }

        $contract_id = absint( $_POST['contract_id'] ?? 0 );
        $version_id = absint( $_POST['version_id'] ?? 0 );
        $user_id = get_current_user_id();

        global $wpdb;
        $table_contracts = $wpdb->prefix . 'seo_contracts';
        $table_versions = $wpdb->prefix . 'seo_contract_versions';

        $contract = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_contracts WHERE id = %d", $contract_id ) );

        if ( ! $contract || ( $contract->user_id != $user_id && ! current_user_can( 'manage_seo_contracts' ) ) ) {
            wp_die( 'شما دسترسی لازم برای پذیرش این قرارداد را ندارید.', 'عدم دسترسی', array( 'response' => 403 ) );
        }

        $version = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_versions WHERE id = %d AND contract_id = %d", $version_id, $contract_id ) );
        if ( ! $version ) {
            wp_die( 'نسخه قرارداد یافت نشد.', 'خطا', array( 'response' => 404 ) );
        }

        if ( $version->is_locked ) {
            wp_die( 'این نسخه قرارداد قبلاً پذیرفته و قفل شده است.', 'غیرقابل تغییر', array( 'response' => 400 ) );
        }

        $ip = SEO_OVERSIGHT_Security::get_client_ip();
        $ua = sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ?? '' );

        // Freeze / Lock this contract version permanently
        $wpdb->update(
            $table_versions,
            array(
                'accepted_at' => current_time( 'mysql' ),
                'accepted_by_user_id' => $user_id,
                'acceptance_ip' => $ip,
                'acceptance_user_agent' => $ua,
                'is_locked' => 1
            ),
            array( 'id' => $version_id )
        );

        // Update contract status to awaiting_payment
        $wpdb->update(
            $table_contracts,
            array( 'status' => 'awaiting_payment' ),
            array( 'id' => $contract_id )
        );

        SEO_OVERSIGHT_Notifications::send_admin_contract_accepted( $contract->contract_number, $contract->client_name );

        $redirect = SEO_OVERSIGHT_Dashboard::get_dashboard_url( 'contract', array( 'id' => $contract_id, 'msg' => 'accepted' ) );
        wp_safe_redirect( $redirect );
        exit;
    }

    public static function handle_contract_revision_request() {
        if ( ! is_user_logged_in() ) {
            wp_die( 'دسترسی غیرمجاز.', 'خطا', array( 'response' => 403 ) );
        }

        if ( ! isset( $_POST['seo_contract_revision_nonce'] ) || ! wp_verify_nonce( $_POST['seo_contract_revision_nonce'], 'seo_request_revision_action' ) ) {
            wp_die( 'اعتبار امنیتی ناموفق.', 'خطا', array( 'response' => 403 ) );
        }

        $contract_id = absint( $_POST['contract_id'] ?? 0 );
        $revision_notes = sanitize_textarea_field( $_POST['revision_notes'] ?? '' );
        $user_id = get_current_user_id();

        global $wpdb;
        $table_contracts = $wpdb->prefix . 'seo_contracts';
        $contract = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_contracts WHERE id = %d", $contract_id ) );

        if ( ! $contract || ( $contract->user_id != $user_id && ! current_user_can( 'manage_seo_contracts' ) ) ) {
            wp_die( 'دسترسی غیرمجاز.', 'خطا', array( 'response' => 403 ) );
        }

        $wpdb->update(
            $table_contracts,
            array( 'status' => 'revision_requested' ),
            array( 'id' => $contract_id )
        );

        SEO_OVERSIGHT_Notifications::send_admin_contract_revision_requested( $contract->contract_number, $revision_notes );

        $redirect = SEO_OVERSIGHT_Dashboard::get_dashboard_url( 'contract', array( 'id' => $contract_id, 'msg' => 'revision_requested' ) );
        wp_safe_redirect( $redirect );
        exit;
    }

    public static function get_contract( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_contracts';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );
    }

    public static function get_contract_by_number( $number ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_contracts';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE contract_number = %s", $number ) );
    }

    public static function get_contract_version( $version_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_contract_versions';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $version_id ) );
    }

    public static function get_contract_versions( $contract_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_contract_versions';
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE contract_id = %d ORDER BY version_number DESC", $contract_id ) );
    }

    public static function render_default_persian_contract_text( $plan, $client_data ) {
        $client_name = esc_html( $client_data['client_name'] ?? 'مشتری' );
        $client_company = ! empty( $client_data['client_company'] ) ? 'به نمایندگی از ' . esc_html( $client_data['client_company'] ) : '';
        $client_national_id = ! empty( $client_data['client_national_id'] ) ? 'به شماره ملی/ثبتی ' . esc_html( $client_data['client_national_id'] ) : '';
        $website_url = esc_url( $client_data['website_url'] ?? '' );
        $price_formatted = number_format( $plan->monthly_price_toman ) . ' تومان';

        $terms_notice = '<div class="admin-legal-notice" style="background:#fffbe6; border:1px solid #ffe58f; padding:12px; margin-bottom:20px; font-size:13px; color:#8c6c00; border-radius:4px;">
            <strong>تذکر حقوقی مهم:</strong> این نمونه قرارداد به عنوان پیش‌نویس اولیه ارائه شده است. توصیه می‌شود قبل از شروع فعالیت تجاری رسمی، متون قرارداد توسط مشاور حقوقی یا وکیل ذی‌صلاح بررسی و تایید گردد.
        </div>';

        $body = $terms_notice . "
        <div class='seo-contract-document'>
            <h2 style='text-align:center;'>عنوان قرارداد: قرارداد ارائه خدمات نظارت مستقل بر عملکرد سئو</h2>

            <h3>ماده ۱ — طرفین قرارداد</h3>
            <p>این قرارداد بین ارائه دهنده خدمات نظارت مستقل سئو (پلتفرم پایش سئو) از یک سو، و جناب آقای/خانم/شرکت <strong>{$client_name}</strong> {$client_company} {$client_national_id} به عنوان «کارفرما» از سوی دیگر جهت نظارت بر آدرس وب‌سایت <strong>{$website_url}</strong> منعقد می‌گردد.</p>

            <h3>ماده ۲ — موضوع قرارداد</h3>
            <p>موضوع قرارداد عبارت است از ارزیابی، پایش و نظارت مستمر و مستقل بر عملکرد، سلامت فنی، کیفیت محتوایی و اقدامات سئوی وب‌سایت کارفرما، استخراج شواهد فنی، شناسایی آسیب‌ها و ارائه گزارش‌های دوره‌ای مدیریتی همراه با راهکارهای اصلاحی.</p>

            <h3>ماده ۳ — دامنه خدمات</h3>
            <p>خدمات ارائه‌شده بر اساس پلن انتخاب‌شده (<strong>{$plan->name}</strong>) شامل موارد زیر می‌باشد:</p>
            <ul>";

        $included = explode( "\n", $plan->included_features );
        foreach ( $included as $inc ) {
            if ( trim( $inc ) ) {
                $body .= "<li>" . esc_html( trim( $inc ) ) . "</li>";
            }
        }

        $body .= "</ul>

            <h3>ماده ۴ — خدمات خارج از دامنه (Exclusions)</h3>
            <p>خدمات زیر صراحتاً از موضوع این قرارداد خارج بوده و ارائه‌دهنده هیچ‌گونه مسئولیتی در قبال اجرای مستقیم آن‌ها ندارد:</p>
            <ul>";

        $excluded = explode( "\n", $plan->excluded_features );
        foreach ( $excluded as $exc ) {
            if ( trim( $exc ) ) {
                $body .= "<li>" . esc_html( trim( $exc ) ) . "</li>";
            }
        }

        $body .= "</ul>

            <h3>ماده ۵ — تعهدات ارائه‌دهنده</h3>
            <p>۱. انجام ارزیابی‌ها در بالاترین سطح استانداردهای فنی و حرفه‌ای سئو.<br>
            ۲. ارزیابی مبتنی بر شواهد واقعی و تفکیک فرضیات از مشکلات اثبات‌شده.<br>
            ۳. حفظ محرمانگی کامل اطلاعات فنی، مالی و داده‌های سرچ کنسول و آنالیتیکس کارفرما.<br>
            ۴. تحویل گزارش‌های دوره‌ای طبق زمان‌بندی توافق‌شده.</p>

            <h3>ماده ۶ — تعهدات کارفرما</h3>
            <p>۱. ارائه دسترسی‌های لازم (سطح Read-Only/Viewer) به سرچ کنسول و ابزارهای تحلیلی.<br>
            ۲. پرداخت به موقع مبالغ صورت‌حساب بر اساس مفاد ماده ۹.<br>
            ۳. هماهنگی با تیم مجری یا متخصص سئوی درونی جهت بررسی و اجرای توصیه‌های ناظر.</p>

            <h3>ماده ۷ — استقلال حرفه‌ای</h3>
            <p>ناظر به عنوان یک نهاد ارزیابی‌کننده بی‌پرفورمنس و مستقل عمل می‌نماید. ناظر مجری سئو نبوده و هدف آن ارتقای کیفیت و شفافیت در پروژه است، نه تخریب یا جبهه‌گیری در برابر مجری یا تیم سئوی کارفرما.</p>

            <h3>ماده ۸ — گزارش‌دهی و پیگیری</h3>
            <p>گزارش‌ها به‌صورت الکترونیکی و از طریق داشبورد اختصاصی کارفرما ارائه می‌گردد. جلسات هم‌افزایی طبق تعرفه پلن مربوطه برگزار خواهد شد.</p>

            <h3>ماده ۹ — مبلغ قرارداد و نحوه پرداخت</h3>
            <p>مبلغ ماهانه این قرارداد برابر با <strong>{$price_formatted}</strong> می‌باشد. پرداخت فقط از طریق واریز کارت به کارت به شماره حساب‌های اعلامی در سامانه صورت گرفته و فعال‌سازی خدمات منوط به تایید فیش واریزی توسط بخش حسابداری خواهد بود.</p>

            <h3>ماده ۱۰ — شروع، مدت و تمدید</h3>
            <p>مدت این قرارداد از تاریخ تایید نهایی و تایید اولین پرداخت آغاز شده و به صورت ماهانه/دوره توافقی قابل تمدید می‌باشد.</p>

            <h3>ماده ۱۱ — تعلیق و خاتمه</h3>
            <p>در صورت عدم پرداخت به موقع یا عدم ارائه دسترسی‌های لازم، ناظر مجاز به تعلیق ارائه خدمات تا رفع موانع می‌باشد.</p>

            <h3>ماده ۱۲ — محرمانگی و حفاظت از اطلاعات</h3>
            <p>طرفین متعهد می‌گردند تمام اسناد، آمار و گزارش‌های مبادله‌شده را کاملاً محرمانه تلقی نمایند.</p>

            <h3>ماده ۱۳ — مالکیت فکری</h3>
            <p>حقوق مادی و معنوی متدولوژی‌ها و ابزارهای اختصاصی متعلق به ناظر بوده و گزارش‌های صادره متعلق به کارفرما می‌باشد.</p>

            <h3>ماده ۱۴ — حدود مسئولیت و عدم تضمین نتیجه</h3>
            <p>از آنجا که الگوریتم‌های موتورهای جستجو و رفتارهای ررقبا متغیر بوده و اجرای اصلاحات بر عهده مجری کارفرما است، ناظر تضمین‌کننده رتبه خاص یا ترافیک مشخصی نمی‌باشد و مسئولیت آن محدود به صحت و دقت ارزیابی‌هاست.</p>

            <h3>ماده ۱۵ — شرایط عمومی</h3>
            <p>هرگونه تغییر در مفاد این قرارداد مستلزم ثبت نسخه جدید و تایید الکترونیکی طرفین در سامانه می‌باشد.</p>

            <h3>ماده ۱۶ — پذیرش و نسخه قرارداد</h3>
            <p>این قرارداد به صورت الکترونیکی و با ثبت زمان، آدرس IP و شناسه کاربری در سامانه ناظر به ثبت رسیده و برای طرفین لازم‌الاجرا می‌باشد.</p>
        </div>";

        return $body;
    }
}
