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
            'validation_attribute' => 'סיסמה',
        ],

        'password_confirmation' => [
            'label' => 'אימות סיסמה',
        ],

        'actions' => [

            'reset' => [
                'label' => 'איפוס סיסמה',
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
