<?php

return [

    'title' => 'ثبت‌نام',

    'heading' => 'ثبت‌نام',

    'actions' => [

        'login' => [
            'before' => 'یا',
            'label' => 'وارد حساب خود شوید.',
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
            'title' => 'Too many registration attempts',
            'body' => 'Please try again in :seconds seconds.',
        ],

    ],

];
