<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Admin {
    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
        add_action( 'admin_post_seo_admin_save_plan', array( __CLASS__, 'handle_save_plan' ) );
        add_action( 'admin_post_seo_admin_update_consultation', array( __CLASS__, 'handle_update_consultation' ) );
        add_action( 'admin_post_seo_admin_update_assessment', array( __CLASS__, 'handle_update_assessment' ) );
        add_action( 'admin_post_seo_admin_verify_payment', array( __CLASS__, 'handle_verify_payment' ) );
        add_action( 'admin_post_seo_admin_save_report', array( __CLASS__, 'handle_save_report' ) );
        add_action( 'admin_post_seo_admin_save_payment_settings', array( __CLASS__, 'handle_save_payment_settings' ) );
    }

    public static function add_admin_menu() {
        add_menu_page(
            'نظارت سئو',
            'نظارت سئو',
            'manage_seo_oversight',
            'seo-oversight-dashboard',
            array( __CLASS__, 'page_dashboard' ),
            'dashicons-chart-bar',
            25
        );

        add_submenu_page(
            'seo-oversight-dashboard',
            'پیش‌خوان مدیریت',
            'پیش‌خوان مدیریت',
            'manage_seo_oversight',
            'seo-oversight-dashboard',
            array( __CLASS__, 'page_dashboard' )
        );

        add_submenu_page(
            'seo-oversight-dashboard',
            'تعرفه و پلن‌ها',
            'تعرفه و پلن‌ها',
            'manage_seo_oversight',
            'seo-oversight-plans',
            array( __CLASS__, 'page_plans' )
        );

        add_submenu_page(
            'seo-oversight-dashboard',
            'درخواست‌های مشاوره',
            'درخواست‌های مشاوره',
            'manage_seo_oversight',
            'seo-oversight-consultations',
            array( __CLASS__, 'page_consultations' )
        );

        add_submenu_page(
            'seo-oversight-dashboard',
            'درخواست‌های ارزیابی',
            'درخواست‌های ارزیابی',
            'manage_seo_oversight',
            'seo-oversight-assessments',
            array( __CLASS__, 'page_assessments' )
        );

        add_submenu_page(
            'seo-oversight-dashboard',
            'مدیریت قراردادها',
            'مدیریت قراردادها',
            'manage_seo_contracts',
            'seo-oversight-contracts',
            array( __CLASS__, 'page_contracts' )
        );

        add_submenu_page(
            'seo-oversight-dashboard',
            'تایید واریزی‌ها',
            'تایید واریزی‌ها',
            'manage_seo_payments',
            'seo-oversight-payments',
            array( __CLASS__, 'page_payments' )
        );

        add_submenu_page(
            'seo-oversight-dashboard',
            'صدور گزارش‌ها',
            'صدور گزارش‌ها',
            'edit_seo_reports',
            'seo-oversight-reports',
            array( __CLASS__, 'page_reports' )
        );

        add_submenu_page(
            'seo-oversight-dashboard',
            'تنظیمات پرداخت و حساب',
            'تنظیمات پرداخت',
            'manage_options',
            'seo-oversight-settings',
            array( __CLASS__, 'page_settings' )
        );
    }

    public static function page_dashboard() {
        global $wpdb;
        $table_consultations = $wpdb->prefix . 'seo_consultations';
        $table_assessments = $wpdb->prefix . 'seo_assessments';
        $table_contracts = $wpdb->prefix . 'seo_contracts';
        $table_payments = $wpdb->prefix . 'seo_payments';

        $new_consultations = $wpdb->get_var( "SELECT COUNT(*) FROM $table_consultations WHERE status = 'new'" );
        $new_assessments = $wpdb->get_var( "SELECT COUNT(*) FROM $table_assessments WHERE status = 'new'" );
        $pending_contracts = $wpdb->get_var( "SELECT COUNT(*) FROM $table_contracts WHERE status = 'submitted' OR status = 'under_review'" );
        $pending_payments = $wpdb->get_var( "SELECT COUNT(*) FROM $table_payments WHERE status = 'pending_verification'" );
        $active_clients = $wpdb->get_var( "SELECT COUNT(*) FROM $table_contracts WHERE status = 'active'" );

        ?>
        <div class="wrap" dir="rtl">
            <h1>پیش‌خوان مدیریت پلتفرم نظارت مستقل سئو</h1>
            <hr>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:20px; margin-top:20px;">
                <div style="background:#fff; border-right:4px solid #10233F; padding:20px; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                    <h3 style="margin:0; font-size:14px; color:#667085;">مشاوره‌های جدید</h3>
                    <p style="font-size:28px; font-weight:bold; margin:10px 0 0 0; color:#10233F;"><?php echo intval( $new_consultations ); ?></p>
                </div>
                <div style="background:#fff; border-right:4px solid #21B8C7; padding:20px; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                    <h3 style="margin:0; font-size:14px; color:#667085;">ارزیابی‌های جدید</h3>
                    <p style="font-size:28px; font-weight:bold; margin:10px 0 0 0; color:#21B8C7;"><?php echo intval( $new_assessments ); ?></p>
                </div>
                <div style="background:#fff; border-right:4px solid #B7791F; padding:20px; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                    <h3 style="margin:0; font-size:14px; color:#667085;">قراردادهای در انتظار بررسی</h3>
                    <p style="font-size:28px; font-weight:bold; margin:10px 0 0 0; color:#B7791F;"><?php echo intval( $pending_contracts ); ?></p>
                </div>
                <div style="background:#fff; border-right:4px solid #C03945; padding:20px; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                    <h3 style="margin:0; font-size:14px; color:#667085;">واریزی‌های نیازمند تایید</h3>
                    <p style="font-size:28px; font-weight:bold; margin:10px 0 0 0; color:#C03945;"><?php echo intval( $pending_payments ); ?></p>
                </div>
                <div style="background:#fff; border-right:4px solid #16845B; padding:20px; box-shadow:0 2px 4px rgba(0,0,0,0.05);">
                    <h3 style="margin:0; font-size:14px; color:#667085;">مشتریان فعال</h3>
                    <p style="font-size:28px; font-weight:bold; margin:10px 0 0 0; color:#16845B;"><?php echo intval( $active_clients ); ?></p>
                </div>
            </div>
        </div>
        <?php
    }

    public static function page_plans() {
        $plans = SEO_OVERSIGHT_Service_Plans::get_all_plans( false );
        $edit_id = absint( $_GET['edit'] ?? 0 );
        $edit_plan = $edit_id ? SEO_OVERSIGHT_Service_Plans::get_plan( $edit_id ) : null;

        ?>
        <div class="wrap" dir="rtl">
            <h1>مدیریت پلن‌ها و تعرفه‌های نظارت سئو</h1>
            <hr>

            <div style="display:flex; gap:30px; margin-top:20px;">
                <div style="flex:1; background:#fff; padding:20px; border:1px solid #ccc;">
                    <h3><?php echo $edit_plan ? 'ویرایش پلن' : 'افزودن پلن جدید'; ?></h3>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                        <input type="hidden" name="action" value="seo_admin_save_plan">
                        <input type="hidden" name="plan_id" value="<?php echo $edit_plan ? esc_attr($edit_plan->id) : 0; ?>">
                        <?php wp_nonce_field( 'seo_admin_save_plan_action', 'seo_admin_plan_nonce' ); ?>

                        <p><label><strong>نام پلن:</strong></label><br>
                        <input type="text" name="name" required style="width:100%;" value="<?php echo $edit_plan ? esc_attr($edit_plan->name) : ''; ?>"></p>

                        <p><label><strong>اسلاگ (نام لاتین):</strong></label><br>
                        <input type="text" name="slug" style="width:100%;" value="<?php echo $edit_plan ? esc_attr($edit_plan->slug) : ''; ?>"></p>

                        <p><label><strong>قیمت ماهانه (تومان):</strong></label><br>
                        <input type="number" name="monthly_price_toman" required style="width:100%;" value="<?php echo $edit_plan ? esc_attr($edit_plan->monthly_price_toman) : 0; ?>"></p>

                        <p><label><strong>توضیح کوتاه:</strong></label><br>
                        <textarea name="short_description" rows="2" style="width:100%;"><?php echo $edit_plan ? esc_textarea($edit_plan->short_description) : ''; ?></textarea></p>

                        <p><label><strong>خدمات شامل شده (هرکدام در یک سطر):</strong></label><br>
                        <textarea name="included_features" rows="5" style="width:100%;"><?php echo $edit_plan ? esc_textarea($edit_plan->included_features) : ''; ?></textarea></p>

                        <p><label><strong>خدمات خارج از دامنه / استثنائات (هرکدام در یک سطر):</strong></label><br>
                        <textarea name="excluded_features" rows="4" style="width:100%;"><?php echo $edit_plan ? esc_textarea($edit_plan->excluded_features) : ''; ?></textarea></p>

                        <p><label><input type="checkbox" name="is_active" value="1" <?php checked( $edit_plan ? $edit_plan->is_active : 1, 1 ); ?>> پلن فعال باشد</label></p>
                        <p><label><input type="checkbox" name="is_featured" value="1" <?php checked( $edit_plan ? $edit_plan->is_featured : 0, 1 ); ?>> پلن ویژه / پیشنهادی باشد</label></p>

                        <button type="submit" class="button button-primary">ذخیره پلن</button>
                    </form>
                </div>

                <div style="flex:2;">
                    <h3>لیست پلن‌های تعریف شده</h3>
                    <table class="widefat fixed striped">
                        <thead>
                            <tr>
                                <th>نام پلن</th>
                                <th>قیمت ماهانه</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $plans as $p ) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html( $p->name ); ?></strong></td>
                                    <td><?php echo number_format( $p->monthly_price_toman ); ?> تومان</td>
                                    <td><?php echo $p->is_active ? '<span style="color:green;">فعال</span>' : '<span style="color:red;">غیرفعال</span>'; ?></td>
                                    <td><a href="<?php echo esc_url( admin_url( 'admin.php?page=seo-oversight-plans&edit=' . $p->id ) ); ?>" class="button button-small">ویرایش</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    public static function handle_save_plan() {
        if ( ! current_user_can( 'manage_seo_oversight' ) || ! wp_verify_nonce( $_POST['seo_admin_plan_nonce'], 'seo_admin_save_plan_action' ) ) {
            wp_die( 'دسترسی غیرمجاز.' );
        }

        $plan_id = absint( $_POST['plan_id'] ?? 0 );
        SEO_OVERSIGHT_Service_Plans::save_plan( $_POST, $plan_id );

        wp_safe_redirect( admin_url( 'admin.php?page=seo-oversight-plans' ) );
        exit;
    }

    public static function page_consultations() {
        $consultations = SEO_OVERSIGHT_Consultations::get_all();

        ?>
        <div class="wrap" dir="rtl">
            <h1>درخواست‌های مشاوره نظارت سئو</h1>
            <hr>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>کد پیگیری</th>
                        <th>نام متقاضی</th>
                        <th>نام کسب‌وکار</th>
                        <th>وب‌سایت</th>
                        <th>موبایل</th>
                        <th>تاریخ</th>
                        <th>وضعیت</th>
                        <th>تغییر وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $consultations as $c ) : ?>
                        <tr>
                            <td><code><?php echo esc_html( $c->reference_id ); ?></code></td>
                            <td><?php echo esc_html( $c->full_name ); ?></td>
                            <td><?php echo esc_html( $c->business_name ); ?></td>
                            <td><a href="<?php echo esc_url( $c->website_url ); ?>" target="_blank" dir="ltr"><?php echo esc_html( $c->website_url ); ?></a></td>
                            <td><code dir="ltr"><?php echo esc_html( $c->mobile ); ?></code></td>
                            <td><?php echo esc_html( $c->created_at ); ?></td>
                            <td><strong><?php echo esc_html( SEO_OVERSIGHT_Dashboard::get_status_label( $c->status ) ); ?></strong></td>
                            <td>
                                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;">
                                    <input type="hidden" name="action" value="seo_admin_update_consultation">
                                    <input type="hidden" name="consultation_id" value="<?php echo esc_attr( $c->id ); ?>">
                                    <?php wp_nonce_field( 'seo_admin_consultation_action', 'seo_admin_c_nonce' ); ?>
                                    <select name="status">
                                        <option value="new" <?php selected( $c->status, 'new' ); ?>>جدید</option>
                                        <option value="under_review" <?php selected( $c->status, 'under_review' ); ?>>در حال بررسی</option>
                                        <option value="contacted" <?php selected( $c->status, 'contacted' ); ?>>تماس گرفته شد</option>
                                        <option value="converted" <?php selected( $c->status, 'converted' ); ?>>تبدیل به مشتری</option>
                                        <option value="closed" <?php selected( $c->status, 'closed' ); ?>>بسته شد</option>
                                    </select>
                                    <button type="submit" class="button button-small">بروزرسانی</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function handle_update_consultation() {
        if ( ! current_user_can( 'manage_seo_oversight' ) || ! wp_verify_nonce( $_POST['seo_admin_c_nonce'], 'seo_admin_consultation_action' ) ) {
            wp_die( 'عدم دسترسی' );
        }
        $id = absint( $_POST['consultation_id'] );
        $status = sanitize_text_field( $_POST['status'] );
        SEO_OVERSIGHT_Consultations::update_status( $id, $status );
        wp_safe_redirect( admin_url( 'admin.php?page=seo-oversight-consultations' ) );
        exit;
    }

    public static function page_assessments() {
        $assessments = SEO_OVERSIGHT_Assessments::get_all();

        ?>
        <div class="wrap" dir="rtl">
            <h1>درخواست‌های ارزیابی فنی سئو</h1>
            <hr>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>کد پیگیری</th>
                        <th>نام متقاضی</th>
                        <th>وب‌سایت</th>
                        <th>نوع ارزیابی</th>
                        <th>ارائه‌دهنده فعلی</th>
                        <th>تاریخ</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $assessments as $a ) : ?>
                        <tr>
                            <td><code><?php echo esc_html( $a->reference_id ); ?></code></td>
                            <td><?php echo esc_html( $a->full_name ); ?></td>
                            <td><a href="<?php echo esc_url( $a->website_url ); ?>" target="_blank" dir="ltr"><?php echo esc_html( $a->website_url ); ?></a></td>
                            <td><?php echo esc_html( $a->assessment_type ); ?></td>
                            <td><?php echo esc_html( $a->current_seo_provider ); ?></td>
                            <td><?php echo esc_html( $a->created_at ); ?></td>
                            <td><strong><?php echo esc_html( SEO_OVERSIGHT_Dashboard::get_status_label( $a->status ) ); ?></strong></td>
                            <td>
                                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;">
                                    <input type="hidden" name="action" value="seo_admin_update_assessment">
                                    <input type="hidden" name="assessment_id" value="<?php echo esc_attr( $a->id ); ?>">
                                    <?php wp_nonce_field( 'seo_admin_assessment_action', 'seo_admin_a_nonce' ); ?>
                                    <select name="status">
                                        <option value="new" <?php selected( $a->status, 'new' ); ?>>جدید</option>
                                        <option value="under_review" <?php selected( $a->status, 'under_review' ); ?>>در حال بررسی</option>
                                        <option value="completed" <?php selected( $a->status, 'completed' ); ?>>تکمیل شد</option>
                                    </select>
                                    <button type="submit" class="button button-small">تغییر</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function handle_update_assessment() {
        if ( ! current_user_can( 'manage_seo_oversight' ) || ! wp_verify_nonce( $_POST['seo_admin_a_nonce'], 'seo_admin_assessment_action' ) ) {
            wp_die( 'عدم دسترسی' );
        }
        $id = absint( $_POST['assessment_id'] );
        $status = sanitize_text_field( $_POST['status'] );
        SEO_OVERSIGHT_Assessments::update_status( $id, $status );
        wp_safe_redirect( admin_url( 'admin.php?page=seo-oversight-assessments' ) );
        exit;
    }

    public static function page_contracts() {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_contracts';
        $contracts = $wpdb->get_results( "SELECT * FROM $table ORDER BY id DESC" );

        ?>
        <div class="wrap" dir="rtl">
            <h1>مدیریت قراردادهای نظارت سئو</h1>
            <hr>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>شماره قرارداد</th>
                        <th>نام مشتری</th>
                        <th>وب‌سایت</th>
                        <th>مبلغ ماهانه</th>
                        <th>تاریخ ثبت</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $contracts as $c ) : ?>
                        <tr>
                            <td><code><?php echo esc_html( $c->contract_number ); ?></code></td>
                            <td><?php echo esc_html( $c->client_name ); ?></td>
                            <td><a href="<?php echo esc_url( $c->website_url ); ?>" target="_blank" dir="ltr"><?php echo esc_html( $c->website_url ); ?></a></td>
                            <td><?php echo number_format( $c->monthly_fee_toman ); ?> تومان</td>
                            <td><?php echo esc_html( $c->created_at ); ?></td>
                            <td><strong><?php echo esc_html( SEO_OVERSIGHT_Dashboard::get_status_label( $c->status ) ); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function page_payments() {
        global $wpdb;
        $table = $wpdb->prefix . 'seo_payments';
        $payments = $wpdb->get_results( "SELECT * FROM $table ORDER BY id DESC" );

        ?>
        <div class="wrap" dir="rtl">
            <h1>بررسی و تایید واریزی‌های کارت به کارت</h1>
            <hr>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>کد پیگیری پرداخت</th>
                        <th>نام واریزکننده</th>
                        <th>مبلغ</th>
                        <th>تاریخ واریز</th>
                        <th>کد ارجاع بانک</th>
                        <th>فیش</th>
                        <th>وضعیت</th>
                        <th>عملیات تایید/رد</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $payments as $p ) :
                        $dl_url = SEO_OVERSIGHT_Payments::get_download_url( $p->id );
                    ?>
                        <tr>
                            <td><code><?php echo esc_html( $p->payment_reference ); ?></code></td>
                            <td><?php echo esc_html( $p->payer_name ); ?></td>
                            <td><?php echo number_format( $p->amount_toman ); ?> تومان</td>
                            <td><?php echo esc_html( $p->transfer_date ); ?></td>
                            <td><code dir="ltr"><?php echo esc_html( $p->transaction_ref ); ?></code></td>
                            <td><?php echo $p->receipt_file_path ? '<a href="' . esc_url( $dl_url ) . '" target="_blank" class="button button-small">مشاهده فیش</a>' : '-'; ?></td>
                            <td><strong><?php echo esc_html( SEO_OVERSIGHT_Dashboard::get_status_label( $p->status ) ); ?></strong></td>
                            <td>
                                <?php if ( $p->status === 'pending_verification' ) : ?>
                                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:inline-block;">
                                        <input type="hidden" name="action" value="seo_admin_verify_payment">
                                        <input type="hidden" name="payment_id" value="<?php echo esc_attr( $p->id ); ?>">
                                        <?php wp_nonce_field( 'seo_admin_verify_payment_action', 'seo_admin_p_nonce' ); ?>
                                        <button type="submit" name="verify_action" value="approve" class="button button-primary button-small">تایید واریز</button>
                                        <button type="submit" name="verify_action" value="reject" class="button button-small" style="color:red;">رد واریز</button>
                                    </form>
                                <?php else : ?>
                                    تکمیل شده
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function handle_verify_payment() {
        if ( ! current_user_can( 'manage_seo_payments' ) || ! wp_verify_nonce( $_POST['seo_admin_p_nonce'], 'seo_admin_verify_payment_action' ) ) {
            wp_die( 'عدم دسترسی' );
        }

        $payment_id = absint( $_POST['payment_id'] );
        $verify_action = sanitize_text_field( $_POST['verify_action'] );
        SEO_OVERSIGHT_Payments::verify_payment( $payment_id, $verify_action, 'تایید/رد شده توسط مدیر حسابداری در پنل وردپرس' );

        wp_safe_redirect( admin_url( 'admin.php?page=seo-oversight-payments' ) );
        exit;
    }

    public static function page_reports() {
        global $wpdb;
        $table_reports = $wpdb->prefix . 'seo_reports';
        $reports = $wpdb->get_results( "SELECT * FROM $table_reports ORDER BY id DESC" );

        $users = get_users( array( 'role__in' => array( 'seo_client', 'administrator' ) ) );

        ?>
        <div class="wrap" dir="rtl">
            <h1>صدور و مدیریت گزارش‌های نظارتی</h1>
            <hr>
            <div style="display:flex; gap:30px; margin-top:20px;">
                <div style="flex:1; background:#fff; padding:20px; border:1px solid #ccc;">
                    <h3>ثبت گزارش جدید برای مشتری</h3>
                    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="seo_admin_save_report">
                        <?php wp_nonce_field( 'seo_admin_save_report_action', 'seo_admin_r_nonce' ); ?>

                        <p><label><strong>مشتری:</strong></label><br>
                        <select name="user_id" required style="width:100%;">
                            <?php foreach ( $users as $u ) : ?>
                                <option value="<?php echo esc_attr( $u->ID ); ?>"><?php echo esc_html( $u->display_name . ' (' . $u->user_email . ')' ); ?></option>
                            <?php endforeach; ?>
                        </select></p>

                        <p><label><strong>عنوان گزارش:</strong></label><br>
                        <input type="text" name="title" required style="width:100%;" placeholder="مثلاً: گزارش تحلیل سئوی فنی - آبان ۱۴۰۳"></p>

                        <p><label><strong>دوره گزارش:</strong></label><br>
                        <input type="text" name="reporting_period" required style="width:100%;" placeholder="آبان ۱۴۰۳"></p>

                        <p><label><strong>خلاصه مدیریتی:</strong></label><br>
                        <textarea name="executive_summary" rows="5" style="width:100%;" required></textarea></p>

                        <p><label><strong>فایل پیوست گزارش (PDF):</strong></label><br>
                        <input type="file" name="report_file" accept=".pdf"></p>

                        <button type="submit" class="button button-primary">انتشار و ارسال گزارش به مشتری</button>
                    </form>
                </div>

                <div style="flex:2;">
                    <h3>گزارش‌های انتشار یافته</h3>
                    <table class="widefat fixed striped">
                        <thead>
                            <tr>
                                <th>عنوان گزارش</th>
                                <th>دوره</th>
                                <th>تاریخ صدور</th>
                                <th>پیوست</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $reports as $r ) : ?>
                                <tr>
                                    <td><strong><?php echo esc_html( $r->title ); ?></strong></td>
                                    <td><?php echo esc_html( $r->reporting_period ); ?></td>
                                    <td><?php echo esc_html( $r->issued_date ); ?></td>
                                    <td><?php echo $r->attachment_file_path ? 'دارد' : 'ندارد'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }

    public static function handle_save_report() {
        if ( ! current_user_can( 'edit_seo_reports' ) || ! wp_verify_nonce( $_POST['seo_admin_r_nonce'], 'seo_admin_save_report_action' ) ) {
            wp_die( 'عدم دسترسی' );
        }

        $user_id = absint( $_POST['user_id'] );
        $title = sanitize_text_field( $_POST['title'] );
        $period = sanitize_text_field( $_POST['reporting_period'] );
        $summary = wp_kses_post( $_POST['executive_summary'] );

        $file_path = '';
        $file_url = '';

        if ( ! empty( $_FILES['report_file']['name'] ) ) {
            $upload_dir = wp_upload_dir();
            $seo_rep_path = $upload_dir['basedir'] . '/seo_private_reports';
            $seo_rep_url = $upload_dir['baseurl'] . '/seo_private_reports';

            if ( ! file_exists( $seo_rep_path ) ) {
                wp_mkdir_p( $seo_rep_path );
                file_put_contents( $seo_rep_path . '/.htaccess', "Options -Indexes\n<Files *>\n  SetHandler default-handler\n</Files>" );
            }

            $ext = pathinfo( $_FILES['report_file']['name'], PATHINFO_EXTENSION );
            $filename = 'report_' . date('Ymd_His') . '_' . wp_generate_password(6, false, false) . '.' . $ext;
            $dest = $seo_rep_path . '/' . $filename;

            if ( move_uploaded_file( $_FILES['report_file']['tmp_name'], $dest ) ) {
                $file_path = $dest;
                $file_url = $seo_rep_url . '/' . $filename;
            }
        }

        SEO_OVERSIGHT_Reports::create_report( array(
            'user_id' => $user_id,
            'title' => $title,
            'reporting_period' => $period,
            'executive_summary' => $summary,
            'attachment_file_path' => $file_path,
            'attachment_file_url' => $file_url,
            'status' => 'published',
            'issued_date' => current_time( 'Y-m-d' )
        ) );

        wp_safe_redirect( admin_url( 'admin.php?page=seo-oversight-reports' ) );
        exit;
    }

    public static function page_settings() {
        $settings = SEO_OVERSIGHT_Payments::get_payment_settings();

        ?>
        <div class="wrap" dir="rtl">
            <h1>تنظیمات حساب بانکی و واریز کارت به کارت</h1>
            <hr>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="max-width:600px; background:#fff; padding:20px; border:1px solid #ccc; margin-top:20px;">
                <input type="hidden" name="action" value="seo_admin_save_payment_settings">
                <?php wp_nonce_field( 'seo_admin_save_p_settings_action', 'seo_admin_ps_nonce' ); ?>

                <p><label><strong>نام دارنده حساب:</strong></label><br>
                <input type="text" name="card_holder" required style="width:100%;" value="<?php echo esc_attr( $settings['card_holder'] ); ?>"></p>

                <p><label><strong>نام بانک:</strong></label><br>
                <input type="text" name="bank_name" required style="width:100%;" value="<?php echo esc_attr( $settings['bank_name'] ); ?>"></p>

                <p><label><strong>شماره کارت (۱۶ رقمی):</strong></label><br>
                <input type="text" name="card_number" required style="width:100%;" dir="ltr" value="<?php echo esc_attr( $settings['card_number'] ); ?>"></p>

                <p><label><strong>شماره حساب:</strong></label><br>
                <input type="text" name="account_number" style="width:100%;" dir="ltr" value="<?php echo esc_attr( $settings['account_number'] ); ?>"></p>

                <p><label><strong>شماره شبا (IBAN):</strong></label><br>
                <input type="text" name="iban" style="width:100%;" dir="ltr" value="<?php echo esc_attr( $settings['iban'] ); ?>"></p>

                <p><label><strong>دستورالعمل و توضیحات پرداخت برای مشتری:</strong></label><br>
                <textarea name="instructions" rows="4" style="width:100%;"><?php echo esc_textarea( $settings['instructions'] ); ?></textarea></p>

                <button type="submit" class="button button-primary">ذخیره تنظیمات پرداخت</button>
            </form>
        </div>
        <?php
    }

    public static function handle_save_payment_settings() {
        if ( ! current_user_can( 'manage_options' ) || ! wp_verify_nonce( $_POST['seo_admin_ps_nonce'], 'seo_admin_save_p_settings_action' ) ) {
            wp_die( 'عدم دسترسی' );
        }

        $data = array(
            'card_holder' => sanitize_text_field( $_POST['card_holder'] ?? '' ),
            'bank_name' => sanitize_text_field( $_POST['bank_name'] ?? '' ),
            'card_number' => sanitize_text_field( $_POST['card_number'] ?? '' ),
            'account_number' => sanitize_text_field( $_POST['account_number'] ?? '' ),
            'iban' => sanitize_text_field( $_POST['iban'] ?? '' ),
            'instructions' => sanitize_textarea_field( $_POST['instructions'] ?? '' ),
        );

        update_option( 'seo_oversight_payment_settings', $data );

        wp_safe_redirect( admin_url( 'admin.php?page=seo-oversight-settings' ) );
        exit;
    }
}
