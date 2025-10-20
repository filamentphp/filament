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
            'title' => 'יותר מדי נסיונות איפוס סיסמה',
            'body' => 'אנא נסה שוב בעוד :seconds שניות.',
        ],

    ],

];
