<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Roles {
    public static function init_roles() {
        add_role(
            'seo_client',
            'مشتری نظارت سئو',
            array(
                'read' => true,
                'view_seo_dashboard' => true,
                'view_seo_contracts' => true,
                'accept_seo_contracts' => true,
                'submit_seo_payments' => true,
                'view_seo_reports' => true,
            )
        );

        add_role(
            'seo_auditor',
            'کارشناس ناظر سئو',
            array(
                'read' => true,
                'view_seo_dashboard' => true,
                'manage_seo_oversight' => true,
                'edit_seo_reports' => true,
                'read_private_posts' => true,
            )
        );

        $admin = get_role( 'administrator' );
        if ( $admin ) {
            $admin->add_cap( 'manage_seo_oversight' );
            $admin->add_cap( 'view_seo_dashboard' );
            $admin->add_cap( 'edit_seo_reports' );
            $admin->add_cap( 'manage_seo_payments' );
            $admin->add_cap( 'manage_seo_contracts' );
        }
    }
}
