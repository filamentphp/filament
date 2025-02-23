<?php

return [

    'title' => 'تسجيل الدخول',

    'heading' => 'الدخول إلى حسابك',

    'actions' => [

        'register' => [
            'before' => 'أو',
            'label' => 'إنشاء حساب',
        ],

        'request_password_reset' => [
            'label' => 'نسيت كلمة المرور؟',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'البريد الإلكتروني',
        ],

        'password' => [
            'label' => 'كلمة المرور',
        ],

        'remember' => [
            'label' => 'تذكرني',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'تسجيل الدخول',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'بيانات الاعتماد هذه غير متطابقة مع البيانات المسجلة لدينا.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'لقد قمت بمحاولات تسجيل دخول كثيرة جدًا',
            'body' => 'يرجى المحاولة مرة أخرى بعد :seconds ثواني.',
        ],

    ],

];
