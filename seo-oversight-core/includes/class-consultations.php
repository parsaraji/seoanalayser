<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Consultations {
    public static function init() {
        add_action( 'admin_post_seo_submit_consultation', array( __CLASS__, 'handle_submission' ) );
        add_action( 'admin_post_nopriv_seo_submit_consultation', array( __CLASS__, 'handle_submission' ) );
    }

    public static function generate_reference_id() {
        return 'CNS-' . date('Ymd') . '-' . strtoupper( wp_generate_password( 6, false, false ) );
    }

    public static function handle_submission() {
        if ( ! isset( $_POST['seo_consultation_nonce'] ) || ! wp_verify_nonce( $_POST['seo_consultation_nonce'], 'seo_submit_consultation_action' ) ) {
            wp_die( 'ارزیابی اعتبار امنیتی (Nonce) ناموفق بود. لطفاً دوباره تلاش کنید.', 'خطای امنیتی', array( 'response' => 403 ) );
        }

        // Honeypot anti-spam
        if ( ! empty( $_POST['website_hp'] ) ) {
            wp_die( 'درخواست غیرمجاز شناسایی شد.', 'خطای هرزنامه', array( 'response' => 400 ) );
        }

        $full_name = sanitize_text_field( $_POST['full_name'] ?? '' );
        $business_name = sanitize_text_field( $_POST['business_name'] ?? '' );
        $website_url = esc_url_raw( $_POST['website_url'] ?? '' );
        $mobile = sanitize_text_field( $_POST['mobile'] ?? '' );
        $email = sanitize_email( $_POST['email'] ?? '' );
        $current_seo_setup = sanitize_text_field( $_POST['current_seo_setup'] ?? '' );
        $website_type = sanitize_text_field( $_POST['website_type'] ?? '' );
        $business_objective = sanitize_textarea_field( $_POST['business_objective'] ?? '' );
        $primary_concerns = sanitize_textarea_field( $_POST['primary_concerns'] ?? '' );
        $preferred_contact_method = sanitize_text_field( $_POST['preferred_contact_method'] ?? 'phone' );
        $preferred_time = sanitize_text_field( $_POST['preferred_time'] ?? '' );
        $additional_notes = sanitize_textarea_field( $_POST['additional_notes'] ?? '' );
        $privacy_consent = isset( $_POST['privacy_consent'] ) ? 1 : 0;

        $redirect_back = wp_get_referer() ? wp_get_referer() : home_url( '/consultation/' );

        if ( empty( $full_name ) || empty( $mobile ) || empty( $website_url ) || ! $privacy_consent ) {
            $url = add_query_arg( 'seo_msg', 'missing_fields', $redirect_back );
            wp_safe_redirect( $url );
            exit;
        }

        // Rate limit check
        $ip = SEO_OVERSIGHT_Security::get_client_ip();
        if ( SEO_OVERSIGHT_Security::is_rate_limited( 'consultation_' . $ip, 3, 300 ) ) {
            wp_die( 'تعداد درخواست‌های شما بیش از حد مجاز است. لطفاً چند دقیقه دیگر تلاش کنید.', 'محدودیت درخواست', array( 'response' => 429 ) );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'seo_consultations';
        $ref_id = self::generate_reference_id();
        $user_id = get_current_user_id();

        $inserted = $wpdb->insert(
            $table,
            array(
                'reference_id' => $ref_id,
                'user_id' => $user_id,
                'full_name' => $full_name,
                'business_name' => $business_name,
                'website_url' => $website_url,
                'mobile' => $mobile,
                'email' => $email,
                'current_seo_setup' => $current_seo_setup,
                'website_type' => $website_type,
                'business_objective' => $business_objective,
                'primary_concerns' => $primary_concerns,
                'preferred_contact_method' => $preferred_contact_method,
                'preferred_time' => $preferred_time,
                'additional_notes' => $additional_notes,
                'status' => 'new'
            )
        );

        if ( $inserted ) {
            SEO_OVERSIGHT_Notifications::send_admin_new_consultation( $ref_id, $full_name, $business_name, $mobile, $website_url );
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
        $table = $wpdb->prefix . 'seo_consultations';
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

    public static function update_status( $id, $status, $admin_notes = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_consultations';
        $data = array( 'status' => sanitize_text_field( $status ) );
        if ( ! empty( $admin_notes ) ) {
            $data['admin_notes'] = sanitize_textarea_field( $admin_notes );
        }
        return $wpdb->update( $table, $data, array( 'id' => absint( $id ) ) );
    }
}
