<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Dashboard {
    public static function init() {
        add_shortcode( 'seo_customer_dashboard', array( __CLASS__, 'render_dashboard_shortcode' ) );
    }

    public static function get_dashboard_url( $tab = 'overview', $extra_args = array() ) {
        $dash_page_id = get_option( 'seo_oversight_dashboard_page_id', 0 );
        $base = $dash_page_id ? get_permalink( $dash_page_id ) : home_url( '/dashboard/' );

        $args = array_merge( array( 'tab' => $tab ), $extra_args );
        return add_query_arg( $args, $base );
    }

    public static function render_dashboard_shortcode() {
        if ( ! is_user_logged_in() ) {
            return '<div class="seo-alert seo-alert-warning">'
                 . '<h4>ورود به حساب کاربری</h4>'
                 . '<p>جهت دسترسی به داشبورد اختصاصی و پایش گزارش‌ها، لطفاً وارد حساب خود شوید.</p>'
                 . '<a href="' . esc_url( wp_login_url( self::get_dashboard_url() ) ) . '" class="seo-btn seo-btn-primary">ورود به حساب</a>'
                 . '</div>';
        }

        $user_id = get_current_user_id();
        $tab = sanitize_text_field( $_GET['tab'] ?? 'overview' );
        $msg = sanitize_text_field( $_GET['msg'] ?? '' );

        ob_start();
        ?>
        <div class="seo-dashboard-wrapper">
            <?php if ( $msg === 'accepted' ) : ?>
                <div class="seo-alert seo-alert-success">قرارداد با موفقیت پذیرفته شد. اکنون می‌توانید نسبت به واریز وجه و ثبت فیش اقدام نمایید.</div>
            <?php elseif ( $msg === 'revision_requested' ) : ?>
                <div class="seo-alert seo-alert-info">درخواست اصلاح قرارداد ثبت گردید. کارشناسان ما به زودی با شما تماس خواهند گرفت.</div>
            <?php elseif ( $msg === 'payment_submitted' ) : ?>
                <div class="seo-alert seo-alert-success">فیش واریزی شما با موفقیت ثبت شد و در صف بررسی بخش حسابداری قرار گرفت.</div>
            <?php endif; ?>

            <nav class="seo-dashboard-tabs">
                <a href="<?php echo esc_url( self::get_dashboard_url( 'overview' ) ); ?>" class="seo-tab-item <?php echo $tab === 'overview' ? 'active' : ''; ?>">پیش‌خوان</a>
                <a href="<?php echo esc_url( self::get_dashboard_url( 'contracts' ) ); ?>" class="seo-tab-item <?php echo $tab === 'contracts' || $tab === 'contract' ? 'active' : ''; ?>">قراردادها</a>
                <a href="<?php echo esc_url( self::get_dashboard_url( 'payments' ) ); ?>" class="seo-tab-item <?php echo $tab === 'payments' ? 'active' : ''; ?>">پرداخت‌ها</a>
                <a href="<?php echo esc_url( self::get_dashboard_url( 'reports' ) ); ?>" class="seo-tab-item <?php echo $tab === 'reports' ? 'active' : ''; ?>">گزارش‌های نظارتی</a>
                <a href="<?php echo esc_url( self::get_dashboard_url( 'requests' ) ); ?>" class="seo-tab-item <?php echo $tab === 'requests' ? 'active' : ''; ?>">درخواست‌های من</a>
                <a href="<?php echo esc_url( wp_logout_url( home_url() ) ); ?>" class="seo-tab-item logout">خروج</a>
            </nav>

            <div class="seo-dashboard-content">
                <?php
                switch ( $tab ) {
                    case 'contracts':
                        self::render_contracts_tab( $user_id );
                        break;
                    case 'contract':
                        self::render_single_contract_tab( $user_id, absint( $_GET['id'] ?? 0 ) );
                        break;
                    case 'payments':
                        self::render_payments_tab( $user_id );
                        break;
                    case 'reports':
                        self::render_reports_tab( $user_id );
                        break;
                    case 'requests':
                        self::render_requests_tab( $user_id );
                        break;
                    case 'overview':
                    default:
                        self::render_overview_tab( $user_id );
                        break;
                }
                ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_overview_tab( $user_id ) {
        global $wpdb;
        $table_contracts = $wpdb->prefix . 'seo_contracts';
        $table_payments = $wpdb->prefix . 'seo_payments';
        $table_reports = $wpdb->prefix . 'seo_reports';

        $active_contract = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_contracts WHERE user_id = %d ORDER BY id DESC LIMIT 1", $user_id ) );
        $latest_reports = SEO_OVERSIGHT_Reports::get_reports_for_user( $user_id );
        $pending_payments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table_payments WHERE user_id = %d AND status = 'pending_verification'", $user_id ) );

        $user_info = get_userdata( $user_id );
        ?>
        <div class="seo-dashboard-card">
            <h3>خوش آمدید، <?php echo esc_html( $user_info->display_name ); ?></h3>
            <p class="text-muted">در این بخش خلاصه وضعیت نظارت بر عملکرد سئوی وب‌سایت خود را مشاهده می‌کنید.</p>
        </div>

        <div class="seo-dashboard-grid">
            <div class="seo-card">
                <h4>وضعیت قرارداد جاری</h4>
                <?php if ( $active_contract ) : ?>
                    <p><strong>شماره قرارداد:</strong> <?php echo esc_html( $active_contract->contract_number ); ?></p>
                    <p><strong>وب‌سایت:</strong> <?php echo esc_html( $active_contract->website_url ); ?></p>
                    <p><strong>وضعیت:</strong> <span class="seo-badge seo-badge-<?php echo esc_attr( $active_contract->status ); ?>"><?php echo esc_html( self::get_status_label( $active_contract->status ) ); ?></span></p>
                    <a href="<?php echo esc_url( self::get_dashboard_url( 'contract', array( 'id' => $active_contract->id ) ) ); ?>" class="seo-btn seo-btn-sm seo-btn-outline">مشاهده جزئیات قرارداد</a>
                <?php else : ?>
                    <p>هیچ قرارداد فعالی برای حساب شما ثبت نشده است.</p>
                    <a href="<?php echo esc_url( home_url('/pricing/') ); ?>" class="seo-btn seo-btn-sm seo-btn-primary">مشاهده پلن‌ها و درخواست قرارداد</a>
                <?php endif; ?>
            </div>

            <div class="seo-card">
                <h4>آخرین گزارش‌های نظارتی</h4>
                <?php if ( ! empty( $latest_reports ) ) :
                    $rep = $latest_reports[0];
                ?>
                    <p><strong>عنوان:</strong> <?php echo esc_html( $rep->title ); ?></p>
                    <p><strong>دوره پایش:</strong> <?php echo esc_html( $rep->reporting_period ); ?></p>
                    <p><strong>تاریخ صدور:</strong> <?php echo esc_html( $rep->issued_date ); ?></p>
                    <a href="<?php echo esc_url( self::get_dashboard_url( 'reports' ) ); ?>" class="seo-btn seo-btn-sm seo-btn-primary">دانلود و مشاهده گزارش‌ها</a>
                <?php else : ?>
                    <p>هنوز تصاویری از گزارش‌های دوره‌ای صادر نشده است.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    private static function render_contracts_tab( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_contracts';
        $contracts = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE user_id = %d ORDER BY id DESC", $user_id ) );

        echo '<h3>قراردادهای من</h3>';

        if ( empty( $contracts ) ) {
            echo '<p>هیچ قراردادی ثبت نشده است.</p>';
            return;
        }

        echo '<div class="table-responsive"><table class="seo-data-table">
            <thead>
                <tr>
                    <th>شماره قرارداد</th>
                    <th>وب‌سایت</th>
                    <th>مبلغ ماهانه</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>';

        foreach ( $contracts as $c ) {
            echo '<tr>
                <td>' . esc_html( $c->contract_number ) . '</td>
                <td>' . esc_html( $c->website_url ) . '</td>
                <td>' . number_format( $c->monthly_fee_toman ) . ' تومان</td>
                <td><span class="seo-badge seo-badge-' . esc_attr( $c->status ) . '">' . esc_html( self::get_status_label( $c->status ) ) . '</span></td>
                <td><a href="' . esc_url( self::get_dashboard_url( 'contract', array( 'id' => $c->id ) ) ) . '" class="seo-btn seo-btn-xs seo-btn-secondary">مشاهده و پذیرش</a></td>
            </tr>';
        }

        echo '</tbody></table></div>';
    }

    private static function render_single_contract_tab( $user_id, $contract_id ) {
        $contract = SEO_OVERSIGHT_Contracts::get_contract( $contract_id );

        if ( ! $contract || ! SEO_OVERSIGHT_Security::check_ownership( $contract->user_id, $user_id ) ) {
            echo '<div class="seo-alert seo-alert-danger">قرارداد موردنظر یافت نشد یا عدم دسترسی.</div>';
            return;
        }

        $versions = SEO_OVERSIGHT_Contracts::get_contract_versions( $contract_id );
        $latest_version = ! empty( $versions ) ? $versions[0] : null;

        echo '<div class="seo-contract-header-actions" style="margin-bottom:20px;">
            <a href="' . esc_url( self::get_dashboard_url( 'contracts' ) ) . '" class="seo-btn seo-btn-outline">&rarr; بازگشت به لیست قراردادها</a>
        </div>';

        echo '<div class="seo-card">';
        echo '<h2>قرارداد شماره: ' . esc_html( $contract->contract_number ) . '</h2>';
        echo '<p><strong>وضعیت:</strong> <span class="seo-badge seo-badge-' . esc_attr( $contract->status ) . '">' . esc_html( self::get_status_label( $contract->status ) ) . '</span></p>';

        if ( $latest_version ) {
            echo '<div class="seo-contract-body-box" style="border:1px solid var(--border-color); padding:24px; background:#fff; border-radius:8px; margin:20px 0; text-align:justify; line-height:1.9;">';
            echo $latest_version->contract_body; // Sanitized at creation
            echo '</div>';

            if ( $latest_version->is_locked ) {
                echo '<div class="seo-alert seo-alert-success">';
                echo '<strong>این نسخه از قرارداد به صورت نهایی پذیرفته شده و قفل گردیده است.</strong><br>';
                echo 'تاریخ پذیرش: ' . esc_html( $latest_version->accepted_at ) . '<br>';
                echo 'آدرس IP پذیرش: ' . esc_html( $latest_version->acceptance_ip );
                echo '</div>';

                if ( $contract->status === 'awaiting_payment' || $contract->status === 'payment_under_review' ) {
                    echo '<hr><div class="seo-payment-section">';
                    echo '<h3>دستورالعمل پرداخت کارت به کارت</h3>';
                    $settings = SEO_OVERSIGHT_Payments::get_payment_settings();
                    echo '<p>' . nl2br( esc_html( $settings['instructions'] ) ) . '</p>';
                    echo '<div class="seo-bank-details-card" style="background:var(--soft-bg); padding:16px; border-radius:6px; margin:15px 0;">';
                    echo '<p><strong>نام دارنده حساب:</strong> ' . esc_html( $settings['card_holder'] ) . '</p>';
                    echo '<p><strong>بانک:</strong> ' . esc_html( $settings['bank_name'] ) . '</p>';
                    echo '<p><strong>شماره کارت:</strong> <code dir="ltr" style="font-size:16px;">' . esc_html( $settings['card_number'] ) . '</code></p>';
                    echo '<p><strong>شماره شبا:</strong> <code dir="ltr">' . esc_html( $settings['iban'] ) . '</code></p>';
                    echo '<p><strong>مبلغ واریزی:</strong> ' . number_format( $contract->monthly_fee_toman ) . ' تومان</p>';
                    echo '</div>';

                    self::render_payment_submission_form( $contract );
                    echo '</div>';
                }
            } else {
                // Actions for unaccepted contract
                echo '<div class="seo-contract-actions" style="display:flex; gap:15px; margin-top:20px; flex-wrap:wrap;">';
                ?>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="seo_accept_contract">
                    <input type="hidden" name="contract_id" value="<?php echo esc_attr( $contract->id ); ?>">
                    <input type="hidden" name="version_id" value="<?php echo esc_attr( $latest_version->id ); ?>">
                    <?php wp_nonce_field( 'seo_accept_contract_action', 'seo_contract_accept_nonce' ); ?>
                    <button type="submit" class="seo-btn seo-btn-success" onclick="return confirm('آیا از تایید و پذیرش مفاد این قرارداد اطمینان دارید؟');">تایید و پذیرش الکترونیکی قرارداد</button>
                </form>

                <button class="seo-btn seo-btn-warning" onclick="document.getElementById('revision-form-box').style.display='block';">درخواست اصلاح مفاد</button>
                <?php
                echo '</div>';

                echo '<div id="revision-form-box" style="display:none; margin-top:20px; background:#fffbe6; padding:15px; border-radius:6px;">';
                echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
                echo '<input type="hidden" name="action" value="seo_request_contract_revision">';
                echo '<input type="hidden" name="contract_id" value="' . esc_attr( $contract->id ) . '">';
                wp_nonce_field( 'seo_request_revision_action', 'seo_contract_revision_nonce' );
                echo '<label><strong>توضیحات و موارد نیازمند اصلاح:</strong></label>';
                echo '<textarea name="revision_notes" rows="4" style="width:100%; margin:8px 0;" required></textarea>';
                echo '<button type="submit" class="seo-btn seo-btn-primary">ارسال درخواست اصلاح</button>';
                echo '</form></div>';
            }
        }

        echo '</div>';
    }

    private static function render_payment_submission_form( $contract ) {
        ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" class="seo-form" style="margin-top:20px;">
            <input type="hidden" name="action" value="seo_submit_payment_receipt">
            <input type="hidden" name="contract_id" value="<?php echo esc_attr( $contract->id ); ?>">
            <?php wp_nonce_field( 'seo_submit_payment_action', 'seo_payment_nonce' ); ?>

            <h4>ثبت فیش واریز کارت به کارت</h4>
            <div class="seo-form-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                <div class="seo-form-group">
                    <label>نام واریزکننده <span class="required">*</span></label>
                    <input type="text" name="payer_name" required value="<?php echo esc_attr( $contract->client_name ); ?>">
                </div>

                <div class="seo-form-group">
                    <label>مبلغ واریزی (تومان) <span class="required">*</span></label>
                    <input type="number" name="amount_toman" required value="<?php echo esc_attr( $contract->monthly_fee_toman ); ?>">
                </div>

                <div class="seo-form-group">
                    <label>تاریخ واریز <span class="required">*</span></label>
                    <input type="date" name="transfer_date" required value="<?php echo esc_attr( current_time('Y-m-d') ); ?>">
                </div>

                <div class="seo-form-group">
                    <label>کد پیگیری / شماره ارجاع تراکنش</label>
                    <input type="text" name="transaction_ref" dir="ltr">
                </div>
            </div>

            <div class="seo-form-group" style="margin-top:15px;">
                <label>تصویر فیش یا فایل PDF پرداخت <span class="required">*</span></label>
                <input type="file" name="receipt_file" accept=".jpg,.jpeg,.png,.pdf" required>
                <small>فرمت‌های مجاز: JPG, PNG, PDF (حداکثر ۵ مگابایت)</small>
            </div>

            <button type="submit" class="seo-btn seo-btn-primary" style="margin-top:15px;">ارسال فیش واریز جهت بررسی</button>
        </form>
        <?php
    }

    private static function render_payments_tab( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_payments';
        $payments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE user_id = %d ORDER BY id DESC", $user_id ) );

        echo '<h3>سوابق و وضعیت پرداخت‌ها</h3>';

        if ( empty( $payments ) ) {
            echo '<p>هیچ سابقه پرداختی ثبت نشده است.</p>';
            return;
        }

        echo '<div class="table-responsive"><table class="seo-data-table">
            <thead>
                <tr>
                    <th>کد پیگیری پرداخت</th>
                    <th>نام واریزکننده</th>
                    <th>مبلغ</th>
                    <th>تاریخ واریز</th>
                    <th>وضعیت</th>
                    <th>فیش</th>
                </tr>
            </thead>
            <tbody>';

        foreach ( $payments as $p ) {
            $dl_url = SEO_OVERSIGHT_Payments::get_download_url( $p->id );
            echo '<tr>
                <td>' . esc_html( $p->payment_reference ) . '</td>
                <td>' . esc_html( $p->payer_name ) . '</td>
                <td>' . number_format( $p->amount_toman ) . ' تومان</td>
                <td>' . esc_html( $p->transfer_date ) . '</td>
                <td><span class="seo-badge seo-badge-' . esc_attr( $p->status ) . '">' . esc_html( self::get_status_label( $p->status ) ) . '</span></td>
                <td>' . ( $p->receipt_file_path ? '<a href="' . esc_url( $dl_url ) . '" target="_blank" class="seo-btn seo-btn-xs seo-btn-outline">دانلود فیش</a>' : '-' ) . '</td>
            </tr>';
        }

        echo '</tbody></table></div>';
    }

    private static function render_reports_tab( $user_id ) {
        $reports = SEO_OVERSIGHT_Reports::get_reports_for_user( $user_id );

        echo '<h3>گزارش‌های دوره‌ای نظارت سئو</h3>';

        if ( empty( $reports ) ) {
            echo '<p>هنوز هیچ گزارشی برای شما صادر نشده است.</p>';
            return;
        }

        foreach ( $reports as $r ) {
            $dl_url = ! empty( $r->attachment_file_path ) ? SEO_OVERSIGHT_Reports::get_download_url( $r->id ) : '';
            echo '<div class="seo-card" style="margin-bottom:20px;">';
            echo '<div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap;">';
            echo '<h4>' . esc_html( $r->title ) . '</h4>';
            echo '<span class="seo-badge seo-badge-info">دوره: ' . esc_html( $r->reporting_period ) . '</span>';
            echo '</div>';
            echo '<p><strong>تاریخ صدور:</strong> ' . esc_html( $r->issued_date ) . '</p>';
            echo '<div class="seo-report-summary-box" style="background:var(--soft-bg); padding:15px; border-radius:6px; margin:15px 0;">';
            echo '<h5>خلاصه مدیریتی:</h5>';
            echo wp_kses_post( $r->executive_summary );
            echo '</div>';

            if ( $dl_url ) {
                echo '<a href="' . esc_url( $dl_url ) . '" class="seo-btn seo-btn-primary">دانلود فایل کامل گزارش (PDF)</a>';
            }
            echo '</div>';
        }
    }

    private static function render_requests_tab( $user_id ) {
        $consultations = SEO_OVERSIGHT_Consultations::get_all( array( 'user_id' => $user_id ) );
        $assessments = SEO_OVERSIGHT_Assessments::get_all( array( 'user_id' => $user_id ) );

        echo '<h3>درخواست‌های مشاوره و ارزیابی</h3>';

        echo '<h4>درخواست‌های مشاوره</h4>';
        if ( empty( $consultations ) ) {
            echo '<p>هیچ درخواست مشاوره‌ای ثبت نشده است.</p>';
        } else {
            echo '<div class="table-responsive"><table class="seo-data-table">
                <thead><tr><th>کد پیگیری</th><th>نام کسب‌وکار</th><th>وب‌سایت</th><th>تاریخ</th><th>وضعیت</th></tr></thead><tbody>';
            foreach ( $consultations as $c ) {
                echo '<tr>
                    <td>' . esc_html( $c->reference_id ) . '</td>
                    <td>' . esc_html( $c->business_name ) . '</td>
                    <td>' . esc_html( $c->website_url ) . '</td>
                    <td>' . esc_html( $c->created_at ) . '</td>
                    <td><span class="seo-badge seo-badge-' . esc_attr( $c->status ) . '">' . esc_html( self::get_status_label( $c->status ) ) . '</span></td>
                </tr>';
            }
            echo '</tbody></table></div>';
        }

        echo '<h4 style="margin-top:30px;">درخواست‌های ارزیابی فنی سئو</h4>';
        if ( empty( $assessments ) ) {
            echo '<p>هیچ درخواست ارزیابی ثبت نشده است.</p>';
        } else {
            echo '<div class="table-responsive"><table class="seo-data-table">
                <thead><tr><th>کد پیگیری</th><th>وب‌سایت</th><th>نوع ارزیابی</th><th>تاریخ</th><th>وضعیت</th></tr></thead><tbody>';
            foreach ( $assessments as $a ) {
                echo '<tr>
                    <td>' . esc_html( $a->reference_id ) . '</td>
                    <td>' . esc_html( $a->website_url ) . '</td>
                    <td>' . esc_html( $a->assessment_type ) . '</td>
                    <td>' . esc_html( $a->created_at ) . '</td>
                    <td><span class="seo-badge seo-badge-' . esc_attr( $a->status ) . '">' . esc_html( self::get_status_label( $a->status ) ) . '</span></td>
                </tr>';
            }
            echo '</tbody></table></div>';
        }
    }

    public static function get_status_label( $status ) {
        $labels = array(
            'new' => 'جدید',
            'submitted' => 'ثبت‌شده',
            'under_review' => 'در حال بررسی',
            'awaiting_payment' => 'در انتظار پرداخت',
            'payment_under_review' => 'فیش در حال بررسی',
            'pending_verification' => 'در انتظار تایید حسابداری',
            'approved' => 'تایید شده',
            'active' => 'فعال',
            'rejected' => 'رد شده',
            'revision_requested' => 'درخواست اصلاح',
            'completed' => 'تکمیل‌شده',
            'suspended' => 'معلق',
            'cancelled' => 'لغو شده'
        );
        return $labels[ $status ] ?? $status;
    }
}
