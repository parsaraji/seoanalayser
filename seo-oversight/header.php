<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
$opts = class_exists( 'SEO_OVERSIGHT_Theme_Options' ) ? SEO_OVERSIGHT_Theme_Options::get_options() : array();
if ( ! empty( $opts['announcement_active'] ) && ! empty( $opts['announcement_text'] ) ) :
?>
    <div class="announcement-bar">
        <div class="container">
            <?php echo esc_html( $opts['announcement_text'] ); ?>
            <?php if ( ! empty( $opts['announcement_link'] ) ) : ?>
                <a href="<?php echo esc_url( $opts['announcement_link'] ); ?>" style="color:#21B8C7; margin-inline-start:10px; font-weight:bold;">ثبت درخواست &larr;</a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<header class="site-header">
    <div class="container header-container">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-logo">
            <?php bloginfo( 'name' ); ?>
            <span class="badge">نظارت مستقل سئو</span>
        </a>

        <nav class="main-navigation" aria-label="منوی اصلی">
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'fallback_cb'    => false,
                ) );
            } else {
                echo '<ul>
                    <li><a href="' . esc_url( home_url( '/' ) ) . '">صفحه اصلی</a></li>
                    <li><a href="' . esc_url( home_url( '/services/' ) ) . '">خدمات نظارتی</a></li>
                    <li><a href="' . esc_url( home_url( '/pricing/' ) ) . '">تعرفه‌ها و پلن‌ها</a></li>
                    <li><a href="' . esc_url( home_url( '/how-it-works/' ) ) . '">نحوه پایش</a></li>
                    <li><a href="' . esc_url( home_url( '/blog/' ) ) . '">وبلاگ تحلیلی</a></li>
                    <li><a href="' . esc_url( home_url( '/contact/' ) ) . '">تماس باما</a></li>
                </ul>';
            }
            ?>
        </nav>

        <div class="header-cta-group">
            <a href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>" class="seo-btn seo-btn-primary">درخواست مشاوره</a>
            <?php if ( is_user_logged_in() ) : ?>
                <a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="seo-btn seo-btn-outline">داشبورد من</a>
            <?php else : ?>
                <a href="<?php echo esc_url( wp_login_url( home_url( '/dashboard/' ) ) ); ?>" class="seo-btn seo-btn-outline">ورود مشتریان</a>
            <?php endif; ?>
        </div>
    </div>
</header>
