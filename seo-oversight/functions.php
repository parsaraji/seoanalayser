<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Setup Theme Supports & Enqueue Scripts
function seo_oversight_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );

    register_nav_menus( array(
        'primary' => 'منوی اصلی سایت',
        'footer'  => 'منوی فوتر'
    ) );
}
add_action( 'after_setup_theme', 'seo_oversight_theme_setup' );

function seo_oversight_enqueue_assets() {
    wp_enqueue_style( 'seo-oversight-main', get_template_directory_uri() . '/assets/css/main.css', array(), '1.0.0' );
    wp_enqueue_style( 'seo-oversight-rtl', get_template_directory_uri() . '/rtl.css', array( 'seo-oversight-main' ), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'seo_oversight_enqueue_assets' );

// Theme Settings Options Page
require_once get_template_directory() . '/inc/theme-options.php';
require_once get_template_directory() . '/inc/seo-metadata.php';
