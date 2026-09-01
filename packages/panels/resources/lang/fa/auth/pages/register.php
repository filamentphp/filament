<?php

return [

    'title' => 'ثبت‌نام',

    'heading' => 'ثبت‌نام',

    'actions' => [

        'login' => [
            'before' => 'یا',
            'label' => 'ورود به حساب کاربری',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ایمیل',
        ],

        'name' => [
            'label' => 'نام',
        ],

        'password' => [
            'label' => 'رمز عبور',
            'validation_attribute' => 'رمز عبور',
        ],

        'password_confirmation' => [
            'label' => 'تایید رمز عبور',
        ],

        'actions' => [

            'register' => [
                'label' => 'ثبت‌نام',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'شما بیش از حد مجاز درخواست ثبت‌نام داشته‌اید.',
            'body' => 'لطفاً :seconds ثانیه دیگر تلاش کنید.',
        ],

    ],

];
