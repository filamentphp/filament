<?php

return [

    'title' => 'אפס את הסיסמה שלך',

    'heading' => 'אפס את הסיסמה שלך',

    'form' => [

        'email' => [
            'label' => 'כתובת דוא"ל',
        ],

        'password' => [
            'label' => 'סיסמה',
            'validation_attribute' => 'סיסמא',
        ],

        'password_confirmation' => [
            'label' => 'אימות סיסמא',
        ],

        'actions' => [

            'reset' => [
                'label' => 'איפוס סיסמא',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'יותר מדי נסיונות לאיפוס',
            'body' => 'נסה שוב בעוד :seconds שניות.',
        ],

    ],

];
