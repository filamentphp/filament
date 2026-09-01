<?php

return [

    'title' => 'بازنشانی رمز عبور',

    'heading' => 'بازنشانی رمز عبور',

    'form' => [

        'email' => [
            'label' => 'ایمیل',
        ],

        'password' => [
            'label' => 'رمز عبور',
            'validation_attribute' => 'رمز عبور',
        ],

        'password_confirmation' => [
            'label' => 'تایید رمز عبور',
        ],

        'actions' => [

            'reset' => [
                'label' => 'بازنشانی رمز عبور',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'شما بیش از حد مجاز درخواست بازنشانی رمز عبور داشته‌اید.',
            'body' => 'لطفاً :seconds ثانیه دیگر تلاش کنید.',
        ],

    ],

];
