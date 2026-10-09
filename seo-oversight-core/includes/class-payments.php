<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Payments {
    public static function init() {
        add_action( 'admin_post_seo_submit_payment_receipt', array( __CLASS__, 'handle_receipt_submission' ) );
        add_action( 'admin_post_seo_download_receipt', array( __CLASS__, 'handle_receipt_download' ) );
    }

    public static function get_payment_settings() {
        $defaults = array(
            'card_holder' => 'پلتفرم نظارت مستقل سئو',
            'bank_name' => 'بانک سامان',
            'card_number' => '۶۲۱۹-۸۶۱۰-۰۰۰۰-۰۰۰۰',
            'account_number' => '۱-۱۲۳۴۵۶۷-۸۵۰-۱۱۰',
            'iban' => 'IR120560000000012345678501',
            'instructions' => 'لطفاً مبلغ فاکتور/قرارداد را به شماره کارت فوق واریز نموده و تصویر فیش یا کد پیگیری واریز را در این فرم بارگذاری نمایید. فعال‌سازی خدمات پس از بررسی و تایید بخش حسابداری انجام می‌گردد.',
            'max_file_size_mb' => 5,
            'allowed_types' => 'jpg,jpeg,png,pdf',
            'notice_email' => get_option( 'admin_email' )
        );
        $saved = get_option( 'seo_oversight_payment_settings', array() );
        return wp_parse_args( $saved, $defaults );
    }

    public static function generate_payment_reference() {
        return 'PAY-' . date('Ymd') . '-' . strtoupper( wp_generate_password( 5, false, false ) );
    }

    public static function handle_receipt_submission() {
        if ( ! is_user_logged_in() ) {
            wp_die( 'لطفاً ابتدا وارد حساب کاربری خود شوید.', 'خطا', array( 'response' => 403 ) );
        }

        if ( ! isset( $_POST['seo_payment_nonce'] ) || ! wp_verify_nonce( $_POST['seo_payment_nonce'], 'seo_submit_payment_action' ) ) {
            wp_die( 'اعتبار سنجی امنیتی فرم ناموفق بود.', 'خطای امنیتی', array( 'response' => 403 ) );
        }

        $contract_id = absint( $_POST['contract_id'] ?? 0 );
        $amount_toman = absint( $_POST['amount_toman'] ?? 0 );
        $payer_name = sanitize_text_field( $_POST['payer_name'] ?? '' );
        $transfer_date = sanitize_text_field( $_POST['transfer_date'] ?? current_time( 'Y-m-d' ) );
        $transaction_ref = sanitize_text_field( $_POST['transaction_ref'] ?? '' );
        $user_id = get_current_user_id();

        global $wpdb;
        $table_contracts = $wpdb->prefix . 'seo_contracts';
        $contract = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_contracts WHERE id = %d", $contract_id ) );

        if ( ! $contract || ( $contract->user_id != $user_id && ! current_user_can( 'manage_seo_payments' ) ) ) {
            wp_die( 'قرارداد معتبر یافت نشد یا عدم دسترسی.', 'خطا', array( 'response' => 403 ) );
        }

        // File upload handling
        $file_path = '';
        $file_url = '';

        if ( ! empty( $_FILES['receipt_file']['name'] ) ) {
            $upload = self::process_receipt_upload( $_FILES['receipt_file'] );
            if ( is_wp_error( $upload ) ) {
                wp_die( esc_html( $upload->get_error_message() ), 'خطای بارگذاری فایل', array( 'response' => 400 ) );
            }
            $file_path = $upload['path'];
            $file_url = $upload['url'];
        }

        $table_payments = $wpdb->prefix . 'seo_payments';
        $payment_ref = self::generate_payment_reference();

        $inserted = $wpdb->insert(
            $table_payments,
            array(
                'payment_reference' => $payment_ref,
                'contract_id' => $contract_id,
                'user_id' => $user_id,
                'amount_toman' => $amount_toman > 0 ? $amount_toman : $contract->monthly_fee_toman,
                'payer_name' => $payer_name,
                'transfer_date' => $transfer_date,
                'transaction_ref' => $transaction_ref,
                'receipt_file_path' => $file_path,
                'receipt_file_url' => $file_url,
                'status' => 'pending_verification'
            )
        );

        if ( $inserted ) {
            // Update contract status
            $wpdb->update(
                $table_contracts,
                array( 'status' => 'payment_under_review' ),
                array( 'id' => $contract_id )
            );

            SEO_OVERSIGHT_Notifications::send_admin_payment_submitted( $payment_ref, $contract->contract_number, $payer_name, $amount_toman );

            $redirect = SEO_OVERSIGHT_Dashboard::get_dashboard_url( 'payments', array( 'msg' => 'payment_submitted' ) );
            wp_safe_redirect( $redirect );
            exit;
        } else {
            wp_die( 'خطا در ثبت فیش پرداخت در پایگاه داده.', 'خطای دیتابیس', array( 'response' => 500 ) );
        }
    }

    public static function process_receipt_upload( $file ) {
        $settings = self::get_payment_settings();
        $max_bytes = $settings['max_file_size_mb'] * 1024 * 1024;

        if ( $file['size'] > $max_bytes ) {
            return new WP_Error( 'file_too_large', 'حجم فایل واریزی بیش از حد مجاز (' . $settings['max_file_size_mb'] . ' مگابایت) است.' );
        }

        $file_type = wp_check_filetype( basename( $file['name'] ) );
        $ext = strtolower( $file_type['ext'] );
        $allowed = array_map( 'trim', explode( ',', strtolower( $settings['allowed_types'] ) ) );

        if ( ! in_array( $ext, $allowed, true ) ) {
            return new WP_Error( 'invalid_type', 'فرمت فایل بارگذاری‌شده مجاز نیست. پسوندهای مجاز: ' . implode( ', ', $allowed ) );
        }

        // Upload to protected uploads folder
        $upload_dir = wp_upload_dir();
        $seo_upload_path = $upload_dir['basedir'] . '/seo_private_receipts';
        $seo_upload_url = $upload_dir['baseurl'] . '/seo_private_receipts';

        if ( ! file_exists( $seo_upload_path ) ) {
            wp_mkdir_p( $seo_upload_path );
            // Put .htaccess to prevent direct execution
            file_put_contents( $seo_upload_path . '/.htaccess', "Options -Indexes\n<Files *>\n  SetHandler default-handler\n</Files>" );
        }

        $safe_filename = 'receipt_' . date('Ymd_His') . '_' . wp_generate_password(8, false, false) . '.' . $ext;
        $destination = $seo_upload_path . '/' . $safe_filename;

        if ( ! move_uploaded_file( $file['tmp_name'], $destination ) ) {
            return new WP_Error( 'move_failed', 'انتقال و ذخیره‌سازی فایل با خطا مواجه شد.' );
        }

        return array(
            'path' => $destination,
            'url' => $seo_upload_url . '/' . $safe_filename
        );
    }

    public static function handle_receipt_download() {
        if ( ! is_user_logged_in() ) {
            wp_die( 'دسترسی غیرمجاز.', 'خطا', array( 'response' => 403 ) );
        }

        $payment_id = absint( $_GET['id'] ?? 0 );
        $nonce = sanitize_text_field( $_GET['_wpnonce'] ?? '' );

        if ( ! wp_verify_nonce( $nonce, 'seo_download_receipt_' . $payment_id ) ) {
            wp_die( 'اعتبارسنجی امنیتی دانلود ناموفق بود.', 'خطای امنیتی', array( 'response' => 403 ) );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'seo_payments';
        $payment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $payment_id ) );

        if ( ! $payment ) {
            wp_die( 'فایل یا رکورد واریز یافت نشد.', 'خطا', array( 'response' => 404 ) );
        }

        if ( $payment->user_id != get_current_user_id() && ! current_user_can( 'manage_seo_payments' ) ) {
            wp_die( 'شما اجازه مشاهده این فیش را ندارید.', 'عدم دسترسی', array( 'response' => 403 ) );
        }

        if ( empty( $payment->receipt_file_path ) || ! file_exists( $payment->receipt_file_path ) ) {
            wp_die( 'فایل فیش روی سرور وجود ندارد.', 'خطا', array( 'response' => 404 ) );
        }

        $file_path = $payment->receipt_file_path;
        $mime = mime_content_type( $file_path );
        $filename = basename( $file_path );

        header( 'Content-Type: ' . $mime );
        header( 'Content-Disposition: inline; filename="' . $filename . '"' );
        header( 'Content-Length: ' . filesize( $file_path ) );
        readfile( $file_path );
        exit;
    }

    public static function get_download_url( $payment_id ) {
        $nonce = wp_create_nonce( 'seo_download_receipt_' . $payment_id );
        return admin_url( 'admin-post.php?action=seo_download_receipt&id=' . $payment_id . '&_wpnonce=' . $nonce );
    }

    public static function verify_payment( $payment_id, $action, $admin_notes = '' ) {
        if ( ! current_user_can( 'manage_seo_payments' ) ) {
            return false;
        }

        global $wpdb;
        $table_payments = $wpdb->prefix . 'seo_payments';
        $table_contracts = $wpdb->prefix . 'seo_contracts';

        $payment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_payments WHERE id = %d", $payment_id ) );
        if ( ! $payment ) {
            return false;
        }

        $new_status = ( $action === 'approve' ) ? 'approved' : 'rejected';

        $wpdb->update(
            $table_payments,
            array(
                'status' => $new_status,
                'admin_notes' => sanitize_textarea_field( $admin_notes ),
                'verified_by' => get_current_user_id(),
                'verified_at' => current_time( 'mysql' )
            ),
            array( 'id' => $payment_id )
        );

        // Update contract status
        if ( $action === 'approve' ) {
            $wpdb->update(
                $table_contracts,
                array( 'status' => 'active', 'start_date' => current_time( 'Y-m-d' ) ),
                array( 'id' => $payment->contract_id )
            );
            SEO_OVERSIGHT_Notifications::send_client_payment_approved( $payment->user_id, $payment->payment_reference );
        } else {
            $wpdb->update(
                $table_contracts,
                array( 'status' => 'awaiting_payment' ),
                array( 'id' => $payment->contract_id )
            );
            SEO_OVERSIGHT_Notifications::send_client_payment_rejected( $payment->user_id, $payment->payment_reference, $admin_notes );
        }

        return true;
    }
}
