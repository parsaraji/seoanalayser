<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Reports {
    public static function init() {
        add_action( 'admin_post_seo_download_report_file', array( __CLASS__, 'handle_file_download' ) );
    }

    public static function create_report( $data ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_reports';

        $fields = array(
            'user_id' => absint( $data['user_id'] ),
            'contract_id' => absint( $data['contract_id'] ?? 0 ),
            'title' => sanitize_text_field( $data['title'] ),
            'reporting_period' => sanitize_text_field( $data['reporting_period'] ),
            'executive_summary' => wp_kses_post( $data['executive_summary'] ),
            'findings_json' => wp_json_encode( $data['findings'] ?? array(), JSON_UNESCAPED_UNICODE ),
            'attachment_file_path' => sanitize_text_field( $data['attachment_file_path'] ?? '' ),
            'attachment_file_url' => esc_url_raw( $data['attachment_file_url'] ?? '' ),
            'status' => sanitize_text_field( $data['status'] ?? 'published' ),
            'internal_notes' => sanitize_textarea_field( $data['internal_notes'] ?? '' ),
            'issued_date' => sanitize_text_field( $data['issued_date'] ?? current_time( 'Y-m-d' ) ),
        );

        $inserted = $wpdb->insert( $table, $fields );
        $report_id = $wpdb->insert_id;

        if ( $inserted && $fields['status'] === 'published' ) {
            SEO_OVERSIGHT_Notifications::send_client_new_report( $fields['user_id'], $fields['title'] );
        }

        return $report_id;
    }

    public static function get_reports_for_user( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_reports';
        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE user_id = %d AND status = 'published' ORDER BY issued_date DESC, id DESC", $user_id ) );
    }

    public static function get_report( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_reports';
        return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id ) );
    }

    public static function handle_file_download() {
        if ( ! is_user_logged_in() ) {
            wp_die( 'دسترسی غیرمجاز.', 'خطا', array( 'response' => 403 ) );
        }

        $report_id = absint( $_GET['id'] ?? 0 );
        $nonce = sanitize_text_field( $_GET['_wpnonce'] ?? '' );

        if ( ! wp_verify_nonce( $nonce, 'seo_download_report_' . $report_id ) ) {
            wp_die( 'اعتبارسنجی دانلود ناموفق بود.', 'خطای امنیتی', array( 'response' => 403 ) );
        }

        $report = self::get_report( $report_id );
        if ( ! $report ) {
            wp_die( 'گزارش یافت نشد.', 'خطا', array( 'response' => 404 ) );
        }

        if ( $report->user_id != get_current_user_id() && ! current_user_can( 'manage_seo_oversight' ) ) {
            wp_die( 'شما اجازه مشاهده فایل این گزارش را ندارید.', 'عدم دسترسی', array( 'response' => 403 ) );
        }

        if ( empty( $report->attachment_file_path ) || ! file_exists( $report->attachment_file_path ) ) {
            wp_die( 'فایل پیوست این گزارش یافت نشد.', 'خطا', array( 'response' => 404 ) );
        }

        $file_path = $report->attachment_file_path;
        $mime = mime_content_type( $file_path );
        $filename = basename( $file_path );

        header( 'Content-Type: ' . $mime );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Content-Length: ' . filesize( $file_path ) );
        readfile( $file_path );
        exit;
    }

    public static function get_download_url( $report_id ) {
        $nonce = wp_create_nonce( 'seo_download_report_' . $report_id );
        return admin_url( 'admin-post.php?action=seo_download_report_file&id=' . $report_id . '&_wpnonce=' . $nonce );
    }
}
