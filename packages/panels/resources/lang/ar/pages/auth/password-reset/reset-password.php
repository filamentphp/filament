<?php

return [

    'title' => 'إعادة تعيين كلمة المرور',

    'heading' => 'إعادة تعيين كلمة المرور',

    'form' => [

        'email' => [
            'label' => 'البريد الإلكتروني',
        ],

        'password' => [
            'label' => 'كلمة المرور',
            'validation_attribute' => 'كلمة المرور',
        ],

        'password_confirmation' => [
            'label' => 'تأكيد كلمة المرور',
        ],

        'actions' => [

            'reset' => [
                'label' => 'إعادة تعيين كلمة المرور',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'محاولات إعادة تعيين كثيرة جداً. يرجى المحاولة مرة أخرى بعد :seconds ثواني.',
        ],

    ],

];
