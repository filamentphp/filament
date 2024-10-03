<?php

return [

    'title' => 'הרשמה',

    'heading' => 'הירשם',

    'actions' => [

        'login' => [
            'before' => 'או',
            'label' => 'התחבר לחשבונך',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'כתובת דוא"ל',
        ],

        'name' => [
            'label' => 'שם',
        ],

        'password' => [
            'label' => 'סיסמה',
            'validation_attribute' => 'סיסמה',
        ],

        'password_confirmation' => [
            'label' => 'אמת סיסמה',
        ],

        'actions' => [

            'register' => [
                'label' => 'הירשם',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'יותר מדי ניסיונות להרשמה',
            'body' => 'אנא נסה שוב בעוד :seconds שניות.',
        ],

    ],

];
