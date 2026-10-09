<?php
/**
 * Template Name: Pricing Plans
 */

get_header();
?>

<div class="container section-padding">
    <div class="section-header">
        <h1>تعرفه‌ها و پلن‌های نظارت مستقل سئو</h1>
        <p>ارزیابی شفاف، گزارش‌دهی مستمر و حفاظت از سرمایه‌گذاری سئوی کسب‌وکار شما</p>
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
                    foreach ( $inc as $feature ) :
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

<?php get_footer(); ?>
