<?php

return [

    'label' => 'פרופיל',

    'form' => [

        'email' => [
            'label' => 'כתובת דוא"ל',
        ],

        'name' => [
            'label' => 'שם',
        ],

        'password' => [
            'label' => 'סיסמה חדשה',
            'validation_attribute' => 'סיסמה',
        ],

        'password_confirmation' => [
            'label' => 'אימות סיסמה חדשה',
        ],

        'actions' => [

            'save' => [
                'label' => 'שמור שינויים',
            ],

        ],

    ],

    'notifications' => [

        'saved' => [
            'title' => 'נשמר',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'ביטול',
        ],

    ],

];
