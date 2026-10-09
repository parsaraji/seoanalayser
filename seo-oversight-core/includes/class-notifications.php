<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SEO_OVERSIGHT_Notifications {
    public static function init() {
        // Hooks
    }

    public static function send_admin_new_consultation( $ref_id, $full_name, $business_name, $mobile, $website_url ) {
        $admin_email = get_option( 'admin_email' );
        $subject = sprintf( '[درخواست مشاوره جدید] %s - %s', $ref_id, $business_name );
        $message = "یک درخواست مشاوره جدید ثبت شد:\n\n"
                 . "کد پیگیری: {$ref_id}\n"
                 . "نام و نام خانوادگی: {$full_name}\n"
                 . "نام کسب‌وکار: {$business_name}\n"
                 . "موبایل: {$mobile}\n"
                 . "وب‌سایت: {$website_url}\n\n"
                 . "جهت مشاهده و مدیریت درخواست به پنل مدیریت سایت مراجعه کنید.";

        wp_mail( $admin_email, $subject, $message );
    }

    public static function send_admin_new_assessment( $ref_id, $full_name, $mobile, $website_url ) {
        $admin_email = get_option( 'admin_email' );
        $subject = sprintf( '[درخواست ارزیابی سئو] %s - %s', $ref_id, $website_url );
        $message = "یک درخواست ارزیابی سئو جدید ثبت شد:\n\n"
                 . "کد پیگیری: {$ref_id}\n"
                 . "نام متقاضی: {$full_name}\n"
                 . "شماره تماس: {$mobile}\n"
                 . "آدرس سایت: {$website_url}\n\n"
                 . "جهت بررسی به بخش ارزیابی‌های پیشرفته مراجعه نمایید.";

        wp_mail( $admin_email, $subject, $message );
    }

    public static function send_admin_new_contract_request( $contract_num, $client_name, $website_url ) {
        $admin_email = get_option( 'admin_email' );
        $subject = sprintf( '[درخواست قرارداد جدید] %s - %s', $contract_num, $client_name );
        $message = "یک درخواست تنظیم قرارداد جدید ثبت شده است:\n\n"
                 . "شماره قرارداد: {$contract_num}\n"
                 . "مشتری: {$client_name}\n"
                 . "وب‌سایت: {$website_url}\n\n"
                 . "لطفاً پیش‌نویس قرارداد را بررسی و جهت تایید مشتری آماده نمایید.";

        wp_mail( $admin_email, $subject, $message );
    }

    public static function send_admin_contract_accepted( $contract_num, $client_name ) {
        $admin_email = get_option( 'admin_email' );
        $subject = sprintf( '[تایید قرارداد توسط مشتری] %s', $contract_num );
        $message = "قرارداد شماره {$contract_num} توسط آقای/خانم {$client_name} به صورت الکترونیکی تایید شد.\n\n"
                 . "وضعیت قرارداد به «در انتظار پرداخت» تغییر یافت.";

        wp_mail( $admin_email, $subject, $message );
    }

    public static function send_admin_contract_revision_requested( $contract_num, $notes ) {
        $admin_email = get_option( 'admin_email' );
        $subject = sprintf( '[درخواست اصلاح قرارداد] %s', $contract_num );
        $message = "مشتری درخواست اصلاح مفاد قرارداد شماره {$contract_num} را ثبت نموده است:\n\n"
                 . "توضیحات مشتری:\n{$notes}";

        wp_mail( $admin_email, $subject, $message );
    }

    public static function send_admin_payment_submitted( $payment_ref, $contract_num, $payer_name, $amount ) {
        $admin_email = get_option( 'admin_email' );
        $subject = sprintf( '[ثبت فیش واریزی] %s - %s', $payment_ref, $contract_num );
        $message = "یک فیش واریزی جدید برای بررسی حسابداری ثبت گردید:\n\n"
                 . "کد پیگیری پرداخت: {$payment_ref}\n"
                 . "شماره قرارداد: {$contract_num}\n"
                 . "نام واریزکننده: {$payer_name}\n"
                 . "مبلغ: " . number_format($amount) . " تومان\n\n"
                 . "لطفاً پس از تطبیق با حساب بانکی، واریزی را تایید یا رد کنید.";

        wp_mail( $admin_email, $subject, $message );
    }

    public static function send_client_payment_approved( $user_id, $payment_ref ) {
        $user = get_userdata( $user_id );
        if ( ! $user || empty( $user->user_email ) ) return;

        $subject = sprintf( 'پرداخت شما تایید شد - کد پیگیری %s', $payment_ref );
        $message = "مشتری گرامی؛\n\n"
                 . "پرداخت شما با شماره پیگیری {$payment_ref} توسط بخش حسابداری تایید گردید و قرارداد شما فعال شد.\n"
                 . "جهت مشاهده وضعیت و دانلود گزارش‌ها به پنل کاربری مراجعه کنید.";

        wp_mail( $user->user_email, $subject, $message );
    }

    public static function send_client_payment_rejected( $user_id, $payment_ref, $reason ) {
        $user = get_userdata( $user_id );
        if ( ! $user || empty( $user->user_email ) ) return;

        $subject = sprintf( 'عدم تایید پرداخت - کد پیگیری %s', $payment_ref );
        $message = "مشتری گرامی؛\n\n"
                 . "فیش واریزی شما با شماره پیگیری {$payment_ref} مورد تایید قرار نگرفت.\n"
                 . "علت عدم تایید: {$reason}\n\n"
                 . "لطفاً جهت پیگیری یا بارگذاری مجدد فیش معتبر به پنل کاربری خود مراجعه فرمایید.";

        wp_mail( $user->user_email, $subject, $message );
    }

    public static function send_client_new_report( $user_id, $report_title ) {
        $user = get_userdata( $user_id );
        if ( ! $user || empty( $user->user_email ) ) return;

        $subject = sprintf( 'گزارش جدید نظارت سئو: %s', $report_title );
        $message = "مشتری گرامی؛\n\n"
                 . "گزارش جدید نظارت مستقل سئو با عنوان «{$report_title}» در پنل کاربری شما قرار گرفت.\n"
                 . "جهت مشاهده خلاصه مدیریتی و دانلود فایل کامل گزارش وارد حساب خود شوید.";

        wp_mail( $user->user_email, $subject, $message );
    }
}
