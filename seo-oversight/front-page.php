<?php
/**
 * Template Name: Front Page
 */

get_header();
$opts = class_exists( 'SEO_OVERSIGHT_Theme_Options' ) ? SEO_OVERSIGHT_Theme_Options::get_options() : array();
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container hero-content">
        <h1><?php echo esc_html( $opts['hero_title'] ); ?></h1>
        <p class="hero-subtitle">
            <?php echo esc_html( $opts['hero_desc'] ); ?>
        </p>
        <div class="header-cta-group" style="justify-content:center; gap:15px; margin-top:2rem;">
            <a href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>" class="seo-btn seo-btn-primary" style="padding:0.85rem 2rem; font-size:1.1rem;">درخواست مشاوره اختصاصی</a>
            <a href="<?php echo esc_url( home_url( '/services/' ) ); ?>" class="seo-btn seo-btn-outline" style="padding:0.85rem 2rem; font-size:1.1rem;">مشاهده خدمات نظارتی</a>
        </div>
    </div>
</section>

<!-- Business Challenges & Value Proposition -->
<section class="section-padding">
    <div class="container">
        <div class="section-header">
            <h2>چالش‌های مدیریت در ارزیابی و نظارت بر سئو</h2>
            <p>چرا صاحبان کسب‌وکارها و مدیران به نظارت مستقل و بی‌پرفورمنس بر سئو نیاز دارند؟</p>
        </div>

        <div class="seo-grid">
            <div class="seo-card">
                <h3>۱. ابهام در خروجی‌های واقعی سئو</h3>
                <p class="justify-text">مدیران اغلب نمی‌دانند هزینه‌های پرداختی ماهانه دقیقاً صرف چه اقدامات فنی یا محتوایی شده و آیا استانداردهای روز گوگل رعایت گردیده است یا خیر.</p>
            </div>
            <div class="seo-card">
                <h3>۲. حل‌نشدن ماندگار مشکلات فنی</h3>
                <p class="justify-text">برخی خطاهای بحرانی سرچ کنسول، خطاهای نمایه‌سازی یا مشکلات سرعت ماه‌ها بدون اصلاح باقی می‌مانند بدون آنکه مدیر از وجود آن‌ها مطلع باشد.</p>
            </div>
            <div class="seo-card">
                <h3>۳. عدم وجود شواهد در گزارش‌ها</h3>
                <p class="justify-text">گزارش‌های سئو اغلب به چند نمودار کلی رتبه محدود می‌شوند و شواهد فنی کافی از انجام درست وظایف یا ریسک‌های بک‌لینک ارائه نمی‌شود.</p>
            </div>
            <div class="seo-card">
                <h3>۴. نیاز به ناظر بی‌طرف و متخصص</h3>
                <p class="justify-text">ارزیابی عملکرد مجری یا تیم داخلی سئو نیاز به دانش عمیق تخصصی دارد. ناظر مستقل به عنوان مشاور امین مدیر، کیفیت اقدامات را ارزیابی می‌کند.</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Overview -->
<section class="section-padding" style="background-color: var(--soft-bg);">
    <div class="container">
        <div class="section-header">
            <h2>محورهای شش‌گانه نظارت مستقل سئو</h2>
            <p>خدمات تخصصی پایش، حسابرسی و ارزیابی کیفیت بدون دخالت مستقیم در اجرای مجری</p>
        </div>

        <div class="seo-grid">
            <div class="seo-card">
                <h3>نظارت بر سئوی فنی (Technical)</h3>
                <p class="justify-text">پایش مداوم کدهای کانونیکال، ساختار داده‌ها، بودجه خزش، نمایه‌سازی و اصلاح خطاهای فنی سرچ کنسول.</p>
                <a href="<?php echo esc_url( home_url( '/services/technical-seo-oversight/' ) ); ?>" class="seo-btn seo-btn-sm seo-btn-outline" style="margin-top:10px;">اطلاعات بیشتر &larr;</a>
            </div>

            <div class="seo-card">
                <h3>بررسی سئوی داخلی و محتوا</h3>
                <p class="justify-text">ارزیابی کیفیت محتوا، هم‌راستایی با قصد جستجوی کاربر، جلوگیری از کانیبالیزیشن و پایش لینک‌سازی داخلی.</p>
                <a href="<?php echo esc_url( home_url( '/services/onpage-content-review/' ) ); ?>" class="seo-btn seo-btn-sm seo-btn-outline" style="margin-top:10px;">اطلاعات بیشتر &larr;</a>
            </div>

            <div class="seo-card">
                <h3>تحلیل عملکرد ارگانیک</h3>
                <p class="justify-text">پایش دقیق نوسانات ترافیک ارگانیک، خطاهای نرخ تبدیل و تحلیل سهم بازار در کلمات کلیدی کلیدی.</p>
                <a href="<?php echo esc_url( home_url( '/services/performance-monitoring/' ) ); ?>" class="seo-btn seo-btn-sm seo-btn-outline" style="margin-top:10px;">اطلاعات بیشتر &larr;</a>
            </div>

            <div class="seo-card">
                <h3>ارزیابی سئوی خارجی (Off-Page)</h3>
                <p class="justify-text">شناسایی بک‌لینک‌های اسپم و مخرب، پایش آنکورتکست‌ها و سنجش ریسک‌های جریمه توسط الگوریتم‌های گوگل.</p>
                <a href="<?php echo esc_url( home_url( '/services/offpage-review/' ) ); ?>" class="seo-btn seo-btn-sm seo-btn-outline" style="margin-top:10px;">اطلاعات بیشتر &larr;</a>
            </div>

            <div class="seo-card">
                <h3>پیگیری وظایف و اصلاحات سئو</h3>
                <p class="justify-text">ردیابی زمان‌بندی اجرای توصیه‌ها توسط مجری سئو یا تیم فنی و صحت‌سنجی نهایی قبل از بستن تیکت‌ها.</p>
                <a href="<?php echo esc_url( home_url( '/services/task-monitoring/' ) ); ?>" class="seo-btn seo-btn-sm seo-btn-outline" style="margin-top:10px;">اطلاعات بیشتر &larr;</a>
            </div>

            <div class="seo-card">
                <h3>گزارش‌های جامع مدیریتی</h3>
                <p class="justify-text">ارائه گزارش‌های دوره‌ای شفاف همراه با خلاصه مدیریتی، شواهد مستند و اولویت‌بندی اقدامات اصلاحی.</p>
                <a href="<?php echo esc_url( home_url( '/pricing/' ) ); ?>" class="seo-btn seo-btn-sm seo-btn-outline" style="margin-top:10px;">مشاهده پلن‌ها &larr;</a>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="section-padding">
    <div class="container">
        <div class="section-header">
            <h2>فرآیند ۴ مرحله‌ای نظارت مستقل</h2>
            <p>مسیر شفاف همکاری از جلسه اولیه تا تحویل گزارش‌های مستمر مدیریتی</p>
        </div>

        <div class="seo-grid">
            <div class="seo-card" style="border-inline-start-color:var(--primary-navy);">
                <div style="font-size:2rem; font-weight:bold; color:var(--primary-navy);">۱</div>
                <h3>مشاوره و اعطای دسترسی</h3>
                <p class="justify-text">بررسی اهداف کسب‌وکار، ثبت درخواست و اعطای دسترسی سطح خواندن (Read-Only) به سرچ کنسول و آنالیتیکس.</p>
            </div>
            <div class="seo-card" style="border-inline-start-color:var(--secondary-blue);">
                <div style="font-size:2rem; font-weight:bold; color:var(--secondary-blue);">۲</div>
                <h3>ارزیابی مبنا (Baseline Audit)</h3>
                <p class="justify-text">استخراج وضعیت موجود، شناسایی خطاهای فنی بحرانی و تدوین نقشه راه اولویت‌بندی‌شده پایش.</p>
            </div>
            <div class="seo-card" style="border-inline-start-color:var(--accent-cyan);">
                <div style="font-size:2rem; font-weight:bold; color:var(--accent-cyan);">۳</div>
                <h3>پایش و پیگیری هفتگی</h3>
                <p class="justify-text">بررسی اقدامات انجام‌شده توسط تیم مجری، صحت‌سنجی کیفی و ثبت یافته‌های جدید در سامانه.</p>
            </div>
            <div class="seo-card" style="border-inline-start-color:var(--success);">
                <div style="font-size:2rem; font-weight:bold; color:var(--success);">۴</div>
                <h3>ارائه گزارش‌های ماهانه</h3>
                <p class="justify-text">ارسال گزارش شفاف تحلیلی در داشبورد اختصاصی و برگزاری جلسه هم‌افزایی با کارفرما و مدیران.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Plans Overview -->
<section class="section-padding" style="background-color: var(--soft-bg);">
    <div class="container">
        <div class="section-header">
            <h2>پلن‌ها و تعرفه‌های خدمات نظارتی</h2>
            <p>انتخاب بهترین سطح نظارت متناسب با حجم وب‌سایت و ساختار تیم سئو</p>
        </div>

        <div class="seo-grid">
            <?php
            if ( class_exists( 'SEO_OVERSIGHT_Service_Plans' ) ) :
                $plans = SEO_OVERSIGHT_Service_Plans::get_all_plans( true );
                foreach ( $plans as $p ) :
            ?>
                <div class="seo-card" style="<?php echo $p->is_featured ? 'border: 2px solid var(--secondary-blue); position:relative;' : ''; ?>">
                    <?php if ( $p->is_featured ) : ?>
                        <span style="position:absolute; top:-12px; left:20px; background:var(--secondary-blue); color:#fff; padding:2px 10px; font-size:0.75rem; border-radius:4px;">پیشنهاد ویژه</span>
                    <?php endif; ?>
                    <h3><?php echo esc_html( $p->name ); ?></h3>
                    <p style="color:var(--muted-text); font-size:0.9rem; min-height:45px; text-align:justify;"><?php echo esc_html( $p->short_description ); ?></p>
                    <div style="font-size:1.6rem; font-weight:bold; color:var(--primary-navy); margin:15px 0;">
                        <?php echo number_format( $p->monthly_price_toman ); ?> <span style="font-size:0.9rem; font-weight:normal;">تومان / ماهانه</span>
                    </div>

                    <ul style="margin:15px 0; padding-inline-start:1.2rem; font-size:0.9rem; line-height:1.8;">
                        <?php
                        $inc = explode( "\n", $p->included_features );
                        foreach ( array_slice( $inc, 0, 5 ) as $feature ) :
                            if ( trim( $feature ) ) echo '<li>' . esc_html( trim( $feature ) ) . '</li>';
                        endforeach;
                        ?>
                    </ul>

                    <a href="<?php echo esc_url( home_url( '/contract-request/?plan=' . $p->id ) ); ?>" class="seo-btn <?php echo $p->is_featured ? 'seo-btn-primary' : 'seo-btn-outline'; ?>" style="width:100%; margin-top:15px;">
                        <?php echo esc_html( $p->cta_label ); ?>
                    </a>
                </div>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Sample Report Preview (Demo Data Labeled) -->
<section class="section-padding">
    <div class="container">
        <div class="section-header">
            <h2>نمونه گزارش تحلیلی ناظر مستقل</h2>
            <p>نمایش ساختار شواهد فنی و نحوه اولویت‌بندی یافته‌ها در سامانه (اطلاعات زیر جنبه نمایشی دارد)</p>
        </div>

        <div class="seo-card" style="background:#fff; border:1px dashed var(--secondary-blue); padding:2rem;">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; margin-bottom:1.5rem; border-bottom:1px solid var(--border-color); padding-bottom:1rem;">
                <div>
                    <span class="seo-badge seo-badge-new" style="margin-bottom:5px;">نمونه گزارش دموی تست</span>
                    <h3 style="margin:0;">گزارش ارزیابی سلامت فنی و نمایه‌سازی — وب‌سایت نمونه</h3>
                </div>
                <div style="font-size:0.875rem; color:var(--muted-text);">دوره پایش: آبان ۱۴۰۳</div>
            </div>

            <div class="seo-article-content">
                <p><strong>خلاصه مدیریتی:</strong> در این دوره پایش، تعداد ۲۴ صفحه جدید دچار خطای Soft 404 گردیده و برچسب Canonical برخی مقالات به اشتباه به صفحه اصلی اشاره می‌کرد که با پیگیری از تیم توسعه، اصلاح شد.</p>

                <table class="seo-data-table" style="margin-top:1rem;">
                    <thead>
                        <tr>
                            <th>عنوان یافته فنی</th>
                            <th>سطح اولویت</th>
                            <th>شواهد و مستندات</th>
                            <th>وضعیت پیگیری</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>اشتباه در تگ Canonical برگه محصول</td>
                            <td><span class="seo-badge" style="background:#FCE8E6; color:#C5221F;">بحرانی</span></td>
                            <td>مشاهده ۱۲ آدرس برگه محصول با کانونیکال اشتباه</td>
                            <td><span class="seo-badge seo-badge-approved">اصلاح شده</span></td>
                        </tr>
                        <tr>
                            <td>افت سرعت لود برگه‌های دسته در گوشی</td>
                            <td><span class="seo-badge" style="background:#FEF7E0; color:#B06000;">متوسط</span></td>
                            <td>افزایش LCP به ۳.۸ ثانیه در ابزار PageSpeed</td>
                            <td><span class="seo-badge seo-badge-awaiting_payment">در حال ارجاع به تیم فنی</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section-padding" style="background-color: var(--soft-bg);">
    <div class="container">
        <div class="section-header">
            <h2>پرسش‌های متداول</h2>
            <p>پاسخ به سوالات کلیدی درباره خدمات نظارت و پایش مستقل سئو</p>
        </div>

        <div style="max-width:800px; margin:0 auto;">
            <div class="seo-card" style="margin-bottom:1rem;">
                <h4>آیا خدمات نظارت مستقل جایگزین مجری یا تیم سئوی ما می‌شود؟</h4>
                <p class="justify-text">خیر. ناظر مستقل به هیچ عنوان جانشین تیم اجرای سئو یا آژانس طرف قرارداد شما نمی‌شود، بلکه به عنوان یک نهاد ارزیابی‌کننده بی‌طرف، کیفیت اقدامات را ارزیابی و گزارش می‌کند.</p>
            </div>
            <div class="seo-card" style="margin-bottom:1rem;">
                <h4>چه سطح دسترسی برای شروع نظارت نیاز است؟</h4>
                <p class="justify-text">تنها دسترسی مشاهده (Viewer / Read-Only) به گوگل سرچ کنسول و ابزارهای تحلیلی (مانند آنالیتیکس) کافی است. هیچ‌گونه دسترسی مدیریتی یا رمز عبور مستقیم وب‌سایت دریافت نمی‌شود.</p>
            </div>
            <div class="seo-card" style="margin-bottom:1rem;">
                <h4>آیا بهبود رتبه‌ها توسط شما تضمین می‌شود؟</h4>
                <p class="justify-text">خیر. به دلیل تغییرات الگوریتم‌های موتورهای جستجو و وابسته بودن رتبه به اجرای دقیق اصلاحات توسط مجری، هیچ تضمین رتبه یا ترافیک داده نمی‌شود.</p>
            </div>
            <div class="seo-card" style="margin-bottom:1rem;">
                <h4>شیوه پرداخت هزینه‌ها به چه صورت است؟</h4>
                <p class="justify-text">پرداخت‌ها به صورت واریز کارت به کارت ماهانه انجام می‌پذیرد و پس از ثبت فیش در سامانه و تایید حسابداری، خدمات دوره جدید فعال می‌گردد.</p>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="section-padding" style="background:var(--primary-navy); color:#fff; text-align:center;">
    <div class="container">
        <h2 style="color:#fff; margin-bottom:1rem;">آیا مایلید از سلامت و کیفیت سئوی وب‌سایت خود مطمئن شوید؟</h2>
        <p style="color:#CBD5E1; max-width:600px; margin:0 auto 2rem auto; font-size:1.1rem; line-height:1.8;">
            همین امروز درخواست مشاوره اولیه یا ارزیابی پیش‌فرض را ثبت کنید تا کارشناسان ناظر ما با شما تماس بگیرند.
        </p>
        <a href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>" class="seo-btn seo-btn-primary" style="background:var(--accent-cyan); color:var(--primary-navy); font-weight:bold; padding:0.85rem 2.5rem; font-size:1.1rem;">درخواست مشاوره فوری</a>
    </div>
</section>

<?php get_footer(); ?>
