<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Service_Plans {
    public static function init() {
        // Hooks if needed
    }

    public static function get_all_plans( $active_only = true ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_plans';
        if ( $active_only ) {
            return $wpdb->get_results( "SELECT * FROM $table WHERE is_active = 1 ORDER BY display_order ASC, id ASC" );
        }
        return $wpdb->get_results( "SELECT * FROM $table ORDER BY display_order ASC, id ASC" );
    }

    public static function get_plan( $id_or_slug ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_plans';
        if ( is_numeric( $id_or_slug ) ) {
            return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $id_or_slug ) );
        } else {
            return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE slug = %s", $id_or_slug ) );
        }
    }

    public static function save_plan( $data, $id = 0 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_plans';

        $fields = array(
            'name' => sanitize_text_field( $data['name'] ?? '' ),
            'slug' => sanitize_title( $data['slug'] ?? $data['name'] ),
            'short_description' => sanitize_textarea_field( $data['short_description'] ?? '' ),
            'detailed_description' => wp_kses_post( $data['detailed_description'] ?? '' ),
            'monthly_price_toman' => absint( $data['monthly_price_toman'] ?? 0 ),
            'one_time_fee_toman' => absint( $data['one_time_fee_toman'] ?? 0 ),
            'billing_interval' => sanitize_text_field( $data['billing_interval'] ?? 'monthly' ),
            'included_features' => sanitize_textarea_field( $data['included_features'] ?? '' ),
            'excluded_features' => sanitize_textarea_field( $data['excluded_features'] ?? '' ),
            'max_monthly_scope' => sanitize_text_field( $data['max_monthly_scope'] ?? '' ),
            'report_frequency' => sanitize_text_field( $data['report_frequency'] ?? 'monthly' ),
            'meeting_allowance' => sanitize_text_field( $data['meeting_allowance'] ?? '' ),
            'support_response' => sanitize_text_field( $data['support_response'] ?? '' ),
            'is_active' => isset( $data['is_active'] ) ? 1 : 0,
            'is_featured' => isset( $data['is_featured'] ) ? 1 : 0,
            'display_order' => intval( $data['display_order'] ?? 0 ),
            'cta_label' => sanitize_text_field( $data['cta_label'] ?? 'درخواست مشاوره' ),
            'cta_destination' => sanitize_text_field( $data['cta_destination'] ?? '#consultation' ),
            'contract_terms_addendum' => wp_kses_post( $data['contract_terms_addendum'] ?? '' ),
        );

        if ( $id > 0 ) {
            $wpdb->update( $table, $fields, array( 'id' => $id ) );
            return $id;
        } else {
            $wpdb->insert( $table, $fields );
            return $wpdb->insert_id;
        }
    }

    public static function delete_plan( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_plans';
        return $wpdb->delete( $table, array( 'id' => $id ) );
    }
}
