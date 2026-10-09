<?php
/**
 * Mock WordPress Environment for Standalone Integration Tests
 */

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

global $wp_tests_options, $wp_tests_data, $wp_tests_user_id;
$wp_tests_options = array();
$wp_tests_data = array();
$wp_tests_user_id = 0;

function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ) {}
function add_role( $role, $display_name, $capabilities ) {}
function get_role( $role ) {
    return new class {
        public function add_cap( $cap ) {}
    };
}
function register_activation_hook( $file, $callback ) {}
function plugin_dir_path( $file ) { return dirname( __DIR__ ) . '/seo-oversight-core/'; }
function plugin_dir_url( $file ) { return 'http://example.com/wp-content/plugins/seo-oversight-core/'; }
function get_template_directory_uri() { return 'http://example.com/wp-content/themes/seo-oversight'; }
function get_template_directory() { return __DIR__ . '/seo-oversight'; }

function get_option( $key, $default = false ) {
    global $wp_tests_options;
    return isset( $wp_tests_options[$key] ) ? $wp_tests_options[$key] : $default;
}
function update_option( $key, $value ) {
    global $wp_tests_options;
    $wp_tests_options[$key] = $value;
    return true;
}
function get_transient( $key ) { return false; }
function set_transient( $key, $val, $expiration ) { return true; }

function wp_verify_nonce( $nonce, $action ) { return $nonce === 'valid_nonce'; }
function wp_create_nonce( $action ) { return 'valid_nonce'; }
function sanitize_text_field( $str ) { return trim( strip_tags( (string) $str ) ); }
function sanitize_textarea_field( $str ) { return trim( strip_tags( (string) $str ) ); }
function sanitize_email( $email ) { return filter_var( $email, FILTER_VALIDATE_EMAIL ) ? $email : ''; }
function sanitize_title( $title ) { return strtolower( preg_replace( '/[^a-zA-Z0-9_-]/', '', (string) $title ) ); }
function esc_url_raw( $url ) { return filter_var( $url, FILTER_VALIDATE_URL ) ? $url : ''; }
function esc_url( $url ) { return $url; }
function esc_html( $str ) { return htmlspecialchars( (string) $str, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( $str ) { return htmlspecialchars( (string) $str, ENT_QUOTES, 'UTF-8' ); }
function esc_textarea( $str ) { return htmlspecialchars( (string) $str, ENT_QUOTES, 'UTF-8' ); }
function wp_kses_post( $str ) { return (string) $str; }
function absint( $val ) { return abs( intval( $val ) ); }
function wp_generate_password( $len = 12, $special = true, $extra = true ) { return 'testpass123'; }
function is_user_logged_in() { global $wp_tests_user_id; return $wp_tests_user_id > 0; }
function get_current_user_id() { global $wp_tests_user_id; return $wp_tests_user_id; }
function current_user_can( $cap ) { global $wp_tests_user_id; return $wp_tests_user_id === 1; }
function wp_mail( $to, $subject, $message ) { return true; }
function current_time( $type ) { return date( 'Y-m-d H:i:s' ); }
function wp_parse_args( $args, $defaults ) { return array_merge( $defaults, (array) $args ); }
function wp_json_encode( $data, $options = 0 ) { return json_encode( $data, $options ); }

class MockWPDB {
    public $prefix = 'wp_';
    public $tables = array();

    public function get_charset_collate() { return 'DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'; }
    public function prepare( $query, ...$args ) {
        if ( is_array( $args[0] ) ) $args = $args[0];
        foreach ( $args as $arg ) {
            $val = is_numeric( $arg ) ? $arg : "'" . addslashes( (string) $arg ) . "'";
            $query = preg_replace( '/%[sd]/', $val, $query, 1 );
        }
        return $query;
    }
    public function get_var( $sql ) { return 0; }
    public function get_row( $sql ) { return null; }
    public function get_results( $sql ) { return array(); }
    public function insert( $table, $data ) { return 1; }
    public function update( $table, $data, $where ) { return 1; }
    public function delete( $table, $where ) { return 1; }
    public $insert_id = 1;
}

$GLOBALS['wpdb'] = new MockWPDB();

require_once dirname( __DIR__ ) . '/seo-oversight-core/includes/class-security.php';
require_once dirname( __DIR__ ) . '/seo-oversight-core/includes/class-service-plans.php';
require_once dirname( __DIR__ ) . '/seo-oversight-core/includes/class-contracts.php';
require_once dirname( __DIR__ ) . '/seo-oversight-core/includes/class-payments.php';

echo "Running Standalone Suite Tests...\n";

// Test 1: Rate limiting logic
$ip = SEO_OVERSIGHT_Security::get_client_ip();
assert( ! empty( $ip ), "IP should be detected" );

// Test 2: Contract text generation
$dummy_plan = new stdClass();
$dummy_plan->name = 'پلن تست';
$dummy_plan->monthly_price_toman = 10000000;
$dummy_plan->included_features = "تست ۱\nتست ۲";
$dummy_plan->excluded_features = "استثنا ۱";

$text = SEO_OVERSIGHT_Contracts::render_default_persian_contract_text( $dummy_plan, array(
    'client_name' => 'علی احمدی',
    'website_url' => 'https://example.com'
) );

assert( strpos( $text, 'علی احمدی' ) !== false, "Contract text must contain client name" );
assert( strpos( $text, 'https://example.com' ) !== false, "Contract text must contain website url" );
assert( strpos( $text, 'ماده ۱ — طرفین قرارداد' ) !== false, "Contract text must contain Article 1" );
assert( strpos( $text, 'ماده ۱۶ — پذیرش و نسخه قرارداد' ) !== false, "Contract text must contain Article 16" );

// Test 3: Payment settings parse
$p_settings = SEO_OVERSIGHT_Payments::get_payment_settings();
assert( isset( $p_settings['card_number'] ), "Payment settings should contain card_number" );

echo "ALL TESTS PASSED SUCCESSFULLY!\n";
