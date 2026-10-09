<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Metadata {
    public static function init() {
        add_action( 'wp_head', array( __CLASS__, 'output_meta_tags' ), 1 );
        add_action( 'wp_head', array( __CLASS__, 'output_json_ld' ), 2 );
    }

    public static function is_seo_plugin_active() {
        return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' );
    }

    public static function output_meta_tags() {
        if ( self::is_seo_plugin_active() ) {
            return; // Let Yoast or RankMath handle meta tags without duplicate fields
        }

        $opts = SEO_OVERSIGHT_Theme_Options::get_options();
        $desc = is_singular() && get_the_excerpt() ? get_the_excerpt() : $opts['meta_desc_default'];

        echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $desc ) ) . '">' . "\n";
        echo '<link rel="canonical" href="' . esc_url( get_pagenum_link() ) . '">' . "\n";

        // Prevent indexation of private customer pages
        if ( is_page( 'dashboard' ) || is_page( 'contract' ) || is_page( 'payment' ) ) {
            echo '<meta name="robots" content="noindex, nofollow, noarchive">' . "\n";
        }
    }

    public static function output_json_ld() {
        if ( self::is_seo_plugin_active() ) {
            return;
        }

        $opts = SEO_OVERSIGHT_Theme_Options::get_options();

        $org_schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => get_bloginfo( 'name' ),
            'url' => home_url(),
            'logo' => get_site_icon_url(),
            'contactPoint' => array(
                '@type' => 'ContactPoint',
                'telephone' => $opts['contact_phone'],
                'contactType' => 'customer service',
                'areaServed' => 'IR',
                'availableLanguage' => 'Persian'
            )
        );

        echo '<script type="application/ld+json">' . wp_json_encode( $org_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
    }
}
SEO_OVERSIGHT_Metadata::init();
