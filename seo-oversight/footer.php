<?php
$opts = class_exists( 'SEO_OVERSIGHT_Theme_Options' ) ? SEO_OVERSIGHT_Theme_Options::get_options() : array();
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col" style="grid-column: span 2;">
                <h4><?php bloginfo( 'name' ); ?> — پلتفرم نظارت مستقل سئو</h4>
                <p style="color:#CBD5E1; text-align:justify; line-height:1.8;">
                    <?php echo esc_html( $opts['footer_about'] ?? '' ); ?>
                </p>
                <p style="margin-top:15px; font-size:0.9rem; color:#94A3B8;">
                    <strong>تلفن:</strong> <?php echo esc_html( $opts['contact_phone'] ?? '' ); ?><br>
                    <strong>ایمیل:</strong> <?php echo esc_html( $opts['contact_email'] ?? '' ); ?><br>
                    <strong>آدرس:</strong> <?php echo esc_html( $opts['contact_address'] ?? '' ); ?>
                </p>
            </div>

            <div class="footer-col">
                <h4>دسترسی سریع</h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">درباره نظارت مستقل</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/how-it-works/' ) ); ?>">چگونگی عملکرد پایش</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>">پلن‌ها و تعرفه‌ها</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>">درخواست مشاوره اختصاصی</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/assessment/' ) ); ?>">درخواست ارزیابی اولیه سئو</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>قوانین و شفافیت</h4>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">حریم خصوصی و حفاظت از داده‌ها</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/terms/' ) ); ?>">شرایط عمومی خدمات</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/contract-terms/' ) ); ?>">نمونه مفاد حقوقی قرارداد</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/payment-instructions/' ) ); ?>">راهنمای واریز کارت به کارت</a></li>
                    <li><a href="<?php echo esc_url( home_url( '/faq/' ) ); ?>">پرسش‌های متداول</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p><?php echo esc_html( $opts['copyright_text'] ?? '' ); ?> — طراحی شده برای پلتفرم نظارت مستقل و شفاف‌سازی سئو</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
