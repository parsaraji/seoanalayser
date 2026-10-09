<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Security {
    public static function init() {
        // Prevent direct execution checks
    }

    public static function get_client_ip() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $parts = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
            $ip = trim( $parts[0] );
        }
        return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '127.0.0.1';
    }

    public static function is_rate_limited( $key, $max_attempts = 5, $decay_seconds = 300 ) {
        $transient_key = 'seo_rate_' . md5( $key );
        $attempts = get_transient( $transient_key );

        if ( false === $attempts ) {
            set_transient( $transient_key, 1, $decay_seconds );
            return false;
        }

        if ( $attempts >= $max_attempts ) {
            return true;
        }

        set_transient( $transient_key, $attempts + 1, $decay_seconds );
        return false;
    }

    public static function check_ownership( $object_user_id, $current_user_id = null ) {
        if ( null === $current_user_id ) {
            $current_user_id = get_current_user_id();
        }

        if ( current_user_can( 'manage_seo_oversight' ) ) {
            return true;
        }

        return ( absint( $object_user_id ) === absint( $current_user_id ) && $current_user_id > 0 );
    }
}
