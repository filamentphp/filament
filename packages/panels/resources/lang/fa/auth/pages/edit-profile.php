<?php

return [

    'label' => 'پروفایل',

    'form' => [

        'email' => [
            'label' => 'ایمیل',
        ],

        'name' => [
            'label' => 'نام',
        ],

        'password' => [
            'label' => 'رمز عبور جدید',
            'validation_attribute' => 'رمزعبور',
        ],

        'password_confirmation' => [
            'label' => 'تایید رمز عبور جدید',
            'validation_attribute' => 'تایید رمزعبور',
        ],

        'current_password' => [
            'label' => 'رمز عبور فعلی',
            'below_content' => 'برای امنیت بیشتر، لطفاً برای ادامه، رمز عبور خود را تأیید کنید.',
            'validation_attribute' => 'رمز عبور فعلی',
        ],

        'actions' => [

            'save' => [
                'label' => 'ذخیره تغییرات',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'احراز هویت دو مرحله‌ای (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'درخواست تغییر ایمیل ارسال شد',
            'body' => 'درخواست تغییر ایمیل به :email ارسال شده است. لطفاً ایمیل خود را بررسی کنید تا تغییر را تأیید کنید.',
        ],

        'saved' => [
            'title' => 'ذخیره شد',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'لغو',
        ],

    ],

];
