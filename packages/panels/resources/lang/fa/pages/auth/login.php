<?php

return [

    'title' => 'ورود',

    'heading' => 'ورود به حساب کاربری',

    'actions' => [

        'register' => [
            'before' => 'یا',
            'label' => 'ایجاد حساب کاربری',
        ],

        'request_password_reset' => [
            'label' => 'رمز عبور خود را فراموش کرده‌اید؟',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'آدرس ایمیل',
        ],

        'password' => [
            'label' => 'رمز عبور',
        ],

        'remember' => [
            'label' => 'مرا به خاطر بسپار',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'ورود',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'مشخصات واردشده با اطلاعات ما سازگار نیست.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'شما بیش از حد مجاز درخواست ورود داشته‌اید.',
            'body' => 'لطفاً :seconds ثانیه دیگر تلاش کنید.',
        ],

    ],

];
