<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Assessments {
    public static function init() {
        add_action( 'admin_post_seo_submit_assessment', array( __CLASS__, 'handle_submission' ) );
        add_action( 'admin_post_nopriv_seo_submit_assessment', array( __CLASS__, 'handle_submission' ) );
    }

    public static function generate_reference_id() {
        return 'ASM-' . date('Ymd') . '-' . strtoupper( wp_generate_password( 6, false, false ) );
    }

    public static function handle_submission() {
        if ( ! isset( $_POST['seo_assessment_nonce'] ) || ! wp_verify_nonce( $_POST['seo_assessment_nonce'], 'seo_submit_assessment_action' ) ) {
            wp_die( 'ارزیابی اعتبار امنیتی (Nonce) ناموفق بود.', 'خطای امنیتی', array( 'response' => 403 ) );
        }

        if ( ! empty( $_POST['website_hp'] ) ) {
            wp_die( 'درخواست غیرمجاز شناسایی شد.', 'خطای هرزنامه', array( 'response' => 400 ) );
        }

        $full_name = sanitize_text_field( $_POST['full_name'] ?? '' );
        $mobile = sanitize_text_field( $_POST['mobile'] ?? '' );
        $email = sanitize_email( $_POST['email'] ?? '' );
        $website_url = esc_url_raw( $_POST['website_url'] ?? '' );
        $business_type = sanitize_text_field( $_POST['business_type'] ?? '' );
        $target_market = sanitize_text_field( $_POST['target_market'] ?? '' );
        $key_landing_pages = sanitize_textarea_field( $_POST['key_landing_pages'] ?? '' );
        $primary_concerns = sanitize_textarea_field( $_POST['primary_concerns'] ?? '' );
        $current_seo_provider = sanitize_text_field( $_POST['current_seo_provider'] ?? '' );
        $analytics_context = sanitize_textarea_field( $_POST['analytics_context'] ?? '' );
        $assessment_type = sanitize_text_field( $_POST['assessment_type'] ?? 'baseline' );
        $privacy_consent = isset( $_POST['privacy_consent'] ) ? 1 : 0;

        $redirect_back = wp_get_referer() ? wp_get_referer() : home_url( '/assessment/' );

        if ( empty( $full_name ) || empty( $mobile ) || empty( $website_url ) || ! $privacy_consent ) {
            $url = add_query_arg( 'seo_msg', 'missing_fields', $redirect_back );
            wp_safe_redirect( $url );
            exit;
        }

        $ip = SEO_OVERSIGHT_Security::get_client_ip();
        if ( SEO_OVERSIGHT_Security::is_rate_limited( 'assessment_' . $ip, 3, 300 ) ) {
            wp_die( 'تعداد درخواست‌های شما بیش از حد مجاز است.', 'محدودیت درخواست', array( 'response' => 429 ) );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'seo_assessments';
        $ref_id = self::generate_reference_id();
        $user_id = get_current_user_id();

        $inserted = $wpdb->insert(
            $table,
            array(
                'reference_id' => $ref_id,
                'user_id' => $user_id,
                'full_name' => $full_name,
                'mobile' => $mobile,
                'email' => $email,
                'website_url' => $website_url,
                'business_type' => $business_type,
                'target_market' => $target_market,
                'key_landing_pages' => $key_landing_pages,
                'primary_concerns' => $primary_concerns,
                'current_seo_provider' => $current_seo_provider,
                'analytics_context' => $analytics_context,
                'assessment_type' => $assessment_type,
                'status' => 'new',
                'priority' => 'medium'
            )
        );

        if ( $inserted ) {
            SEO_OVERSIGHT_Notifications::send_admin_new_assessment( $ref_id, $full_name, $mobile, $website_url );
            $url = add_query_arg( array( 'seo_msg' => 'success', 'ref' => $ref_id ), $redirect_back );
            wp_safe_redirect( $url );
            exit;
        } else {
            $url = add_query_arg( 'seo_msg', 'db_error', $redirect_back );
            wp_safe_redirect( $url );
            exit;
        }
    }

    public static function get_all( $args = array() ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_assessments';
        $where = array('1=1');
        $params = array();

        if ( ! empty( $args['status'] ) ) {
            $where[] = 'status = %s';
            $params[] = $args['status'];
        }

        if ( ! empty( $args['user_id'] ) ) {
            $where[] = 'user_id = %d';
            $params[] = $args['user_id'];
        }

        $where_sql = implode( ' AND ', $where );
        $sql = "SELECT * FROM $table WHERE $where_sql ORDER BY id DESC";

        if ( ! empty( $params ) ) {
            return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
        }
        return $wpdb->get_results( $sql );
    }

    public static function update_status( $id, $status, $priority = 'medium', $admin_notes = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_assessments';
        $data = array(
            'status' => sanitize_text_field( $status ),
            'priority' => sanitize_text_field( $priority )
        );
        if ( ! empty( $admin_notes ) ) {
            $data['admin_notes'] = sanitize_textarea_field( $admin_notes );
        }
        return $wpdb->update( $table, $data, array( 'id' => absint( $id ) ) );
    }
}
