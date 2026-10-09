<?php
/**
 * Plugin Name: SEO Oversight Core
 * Plugin URI: https://seo-oversight.ir
 * Description: Core business logic, workflows, contracts, payments, and dashboard for Independent SEO Oversight Platform.
 * Version: 1.0.0
 * Author: Independent SEO Oversight Team
 * Text Domain: seo-oversight-core
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SEO_OVERSIGHT_CORE_VERSION', '1.0.0' );
define( 'SEO_OVERSIGHT_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'SEO_OVERSIGHT_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-db.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-roles.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-service-plans.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-consultations.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-assessments.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-contracts.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-payments.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-reports.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-notifications.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-security.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-privacy.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-dashboard.php';
require_once SEO_OVERSIGHT_CORE_PATH . 'includes/class-admin.php';

register_activation_hook( __FILE__, function() {
    SEO_OVERSIGHT_DB::init_db();
    SEO_OVERSIGHT_Roles::init_roles();
    flush_rewrite_rules();
} );

add_action( 'plugins_loaded', array( 'SEO_OVERSIGHT_Core', 'init' ) );

class SEO_OVERSIGHT_Core {
    public static function init() {
        SEO_OVERSIGHT_DB::check_version();
        SEO_OVERSIGHT_Service_Plans::init();
        SEO_OVERSIGHT_Consultations::init();
        SEO_OVERSIGHT_Assessments::init();
        SEO_OVERSIGHT_Contracts::init();
        SEO_OVERSIGHT_Payments::init();
        SEO_OVERSIGHT_Reports::init();
        SEO_OVERSIGHT_Notifications::init();
        SEO_OVERSIGHT_Security::init();
        SEO_OVERSIGHT_Dashboard::init();
        if ( is_admin() ) {
            SEO_OVERSIGHT_Admin::init();
        }
    }
}
