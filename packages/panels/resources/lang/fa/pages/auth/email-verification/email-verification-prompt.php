<?php

return [

    'title' => 'تایید آدرس ایمیل',

    'heading' => 'تایید آدرس ایمیل',

    'actions' => [

        'resend_notification' => [
            'label' => 'ارسال مجدد',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'ایمیلی که فرستادیم را دریافت نکردید؟',
        'notification_sent' => 'ما یک ایمیل حاوی دستورات لازم برای بازنشانی رمز عبور به :email فرستادیم.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'ما ایمیل را دوباره فرستادیم.',
        ],

        'notification_resend_throttled' => [
            'title' => 'شما بیش از حد مجاز درخواست ارسال مجدد ایمیل داشته‌اید.',
            'body' => 'لطفاً :seconds ثانیه دیگر تلاش کنید.',
        ],

    ],

];
