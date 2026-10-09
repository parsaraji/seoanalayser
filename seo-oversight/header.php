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
        <div class="container" style="display:flex; justify-content:center; align-items:center; gap:10px;">
            <span><?php echo esc_html( $opts['announcement_text'] ); ?></span>
            <?php if ( ! empty( $opts['announcement_link'] ) ) : ?>
                <a href="<?php echo esc_url( $opts['announcement_link'] ); ?>" style="color:#21B8C7; font-weight:bold;">ثبت درخواست &larr;</a>
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

        <!-- Mobile Toggle Button -->
        <button class="mobile-nav-toggle" id="mobile-nav-toggle" aria-controls="primary-navigation" aria-expanded="false" aria-label="منوی سایت">
            <span class="hamburger-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </button>

        <!-- Main Navigation & Drawer -->
        <nav class="main-navigation" id="primary-navigation" aria-label="منوی اصلی">
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'nav-menu',
                    'fallback_cb'    => false,
                ) );
            } else {
                echo '<ul class="nav-menu">
                    <li><a href="' . esc_url( home_url( '/' ) ) . '">صفحه اصلی</a></li>
                    <li class="menu-item-has-children"><a href="' . esc_url( home_url( '/services/' ) ) . '">خدمات نظارتی &#9662;</a>
                        <ul class="sub-menu">
                            <li><a href="' . esc_url( home_url( '/services/technical-seo-oversight/' ) ) . '">نظارت بر سئوی فنی</a></li>
                            <li><a href="' . esc_url( home_url( '/services/onpage-content-review/' ) ) . '">بررسی سئوی داخلی و محتوا</a></li>
                            <li><a href="' . esc_url( home_url( '/services/performance-monitoring/' ) ) . '">تحلیل عملکرد ارگانیک</a></li>
                            <li><a href="' . esc_url( home_url( '/services/offpage-review/' ) ) . '">ارزیابی سئوی خارجی</a></li>
                            <li><a href="' . esc_url( home_url( '/services/task-monitoring/' ) ) . '">پیگیری اصلاحات سئو</a></li>
                        </ul>
                    </li>
                    <li><a href="' . esc_url( home_url( '/pricing/' ) ) . '">تعرفه‌ها و پلن‌ها</a></li>
                    <li><a href="' . esc_url( home_url( '/how-it-works/' ) ) . '">نحوه پایش</a></li>
                    <li><a href="' . esc_url( home_url( '/blog/' ) ) . '">وبلاگ تحلیلی</a></li>
                    <li><a href="' . esc_url( home_url( '/contact/' ) ) . '">تماس باما</a></li>
                </ul>';
            }
            ?>

            <div class="header-cta-group mobile-cta">
                <a href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>" class="seo-btn seo-btn-primary">درخواست مشاوره</a>
                <?php if ( is_user_logged_in() ) : ?>
                    <a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="seo-btn seo-btn-outline">داشبورد من</a>
                <?php else : ?>
                    <a href="<?php echo esc_url( wp_login_url( home_url( '/dashboard/' ) ) ); ?>" class="seo-btn seo-btn-outline">ورود مشتریان</a>
                <?php endif; ?>
            </div>
        </nav>

        <div class="header-cta-group desktop-cta">
            <a href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>" class="seo-btn seo-btn-primary">درخواست مشاوره</a>
            <?php if ( is_user_logged_in() ) : ?>
                <a href="<?php echo esc_url( home_url( '/dashboard/' ) ); ?>" class="seo-btn seo-btn-outline">داشبورد من</a>
            <?php else : ?>
                <a href="<?php echo esc_url( wp_login_url( home_url( '/dashboard/' ) ) ); ?>" class="seo-btn seo-btn-outline">ورود مشتریان</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<div class="nav-backdrop" id="nav-backdrop"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var toggle = document.getElementById('mobile-nav-toggle');
    var nav = document.getElementById('primary-navigation');
    var backdrop = document.getElementById('nav-backdrop');

    if (toggle && nav) {
        function toggleNav() {
            var expanded = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', !expanded);
            toggle.classList.toggle('is-active');
            nav.classList.toggle('is-open');
            if (backdrop) backdrop.classList.toggle('is-visible');
        }

        toggle.addEventListener('click', toggleNav);
        if (backdrop) backdrop.addEventListener('click', toggleNav);
    }

    // Add mobile toggle buttons to parents with sub-menus
    var hasChildren = document.querySelectorAll('.main-navigation .menu-item-has-children');
    hasChildren.forEach(function(item) {
        var link = item.querySelector('a');
        if (link) {
            var btn = document.createElement('button');
            btn.className = 'submenu-toggle-btn';
            btn.setAttribute('aria-label', 'باز کردن زیرمنو');
            btn.innerHTML = '&#9662;';
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var sub = item.querySelector('.sub-menu');
                if (sub) {
                    sub.classList.toggle('is-open');
                    btn.classList.toggle('is-open');
                }
            });
            item.insertBefore(btn, link.nextSibling);
        }
    });
});
</script>
