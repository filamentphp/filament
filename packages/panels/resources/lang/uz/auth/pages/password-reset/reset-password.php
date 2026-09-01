<?php

return [

    'title' => 'Parolni qayta o\'rnatish',

    'heading' => 'Parolni qayta o\'rnatish',

    'form' => [

        'email' => [
            'label' => 'Elektron pochta manzili',
        ],

        'password' => [
            'label' => 'Parol',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Parolni tasdiqlash',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Parolni qayta o\'rnatish',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Urinishlar juda ko\'p',
            'body' => 'Iltimos, :seconds soniyadan so\'ng qayta urinib ko\'ring.',
        ],

    ],

];
