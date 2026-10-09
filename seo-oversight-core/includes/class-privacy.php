<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Privacy {
    public static function init() {
        add_filter( 'wp_privacy_personal_data_exporters', array( __CLASS__, 'register_exporters' ) );
        add_filter( 'wp_privacy_personal_data_erasers', array( __CLASS__, 'register_erasers' ) );
    }

    public static function register_exporters( $exporters ) {
        $exporters['seo-oversight-customer-data'] = array(
            'exporter_friendly_name' => 'داده‌های مشتریان پلتفرم نظارت سئو',
            'callback'               => array( __CLASS__, 'customer_data_exporter' ),
        );
        return $exporters;
    }

    public static function register_erasers( $erasers ) {
        $erasers['seo-oversight-customer-data'] = array(
            'eraser_friendly_name' => 'پاک‌سازی داده‌های مشتریان نظارت سئو',
            'callback'             => array( __CLASS__, 'customer_data_eraser' ),
        );
        return $erasers;
    }

    public static function customer_data_exporter( $email_address, $page = 1 ) {
        $user = get_user_by( 'email', $email_address );
        $export_items = array();

        if ( ! $user ) {
            return array( 'data' => array(), 'done' => true );
        }

        global $wpdb;
        $table_contracts = $wpdb->prefix . 'seo_contracts';
        $contracts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_contracts WHERE user_id = %d OR client_email = %s", $user->ID, $email_address ) );

        foreach ( $contracts as $c ) {
            $data = array(
                array( 'name' => 'شماره قرارداد', 'value' => $c->contract_number ),
                array( 'name' => 'نام مشتری', 'value' => $c->client_name ),
                array( 'name' => 'موبایل', 'value' => $c->client_mobile ),
                array( 'name' => 'آدرس وب‌سایت', 'value' => $c->website_url ),
                array( 'name' => 'وضعیت قرارداد', 'value' => $c->status ),
            );

            $export_items[] = array(
                'group_id'    => 'seo_contracts',
                'group_label' => 'قراردادهای نظارت سئو',
                'item_id'     => 'contract-' . $c->id,
                'data'        => $data,
            );
        }

        return array( 'data' => $export_items, 'done' => true );
    }

    public static function customer_data_eraser( $email_address, $page = 1 ) {
        $user = get_user_by( 'email', $email_address );
        $items_removed = false;

        if ( $user ) {
            global $wpdb;
            $table_consultations = $wpdb->prefix . 'seo_consultations';
            $table_assessments = $wpdb->prefix . 'seo_assessments';

            $wpdb->update( $table_consultations, array( 'mobile' => '[حذف شده]', 'full_name' => '[حذف شده]' ), array( 'user_id' => $user->ID ) );
            $wpdb->update( $table_assessments, array( 'mobile' => '[حذف شده]', 'full_name' => '[حذف شده]' ), array( 'user_id' => $user->ID ) );

            $items_removed = true;
        }

        return array(
            'items_removed'  => $items_removed,
            'items_retained' => false,
            'messages'       => array( 'داده‌های شخصی درخواست‌ها انطباق با قوانین حریم خصوصی پاک‌سازی شد.' ),
            'done'           => true,
        );
    }
}
SEO_OVERSIGHT_Privacy::init();
