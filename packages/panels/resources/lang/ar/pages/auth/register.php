<?php

return [

    'title' => 'تسجيل',

    'heading' => 'إنشاء حساب',

    'actions' => [

        'login' => [
            'before' => 'أو',
            'label' => 'سجل الدخول لحسابك',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'البريد الإلكتروني',
        ],

        'name' => [
            'label' => 'الاسم',
        ],

        'password' => [
            'label' => 'كلمة المرور',
            'validation_attribute' => 'كلمة المرور',
        ],

        'password_confirmation' => [
            'label' => 'تأكيد كلمة المرور',
        ],

        'actions' => [

            'register' => [
                'label' => 'إنشاء حساب',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'لقد قمت بمحاولات تسجيل كثيرة جدًا',
            'body' => 'يرجى المحاولة مرة أخرى بعد :seconds ثواني.',
        ],

    ],

];
