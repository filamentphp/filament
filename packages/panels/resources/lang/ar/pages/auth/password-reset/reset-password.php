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
            'title' => 'لقد قمت بمحاولات كثيرة جداً لإعادة تعيين كلمة المرور',
            'body' => 'يرجى المحاولة مرة أخرى بعد :seconds ثواني.',
        ],

    ],

];
