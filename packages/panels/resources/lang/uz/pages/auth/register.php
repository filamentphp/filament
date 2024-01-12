<?php

return [

    'title' => 'Ro\'yxatdan o\'tish',

    'heading' => 'Hisob qaydnomasini ro\'yxatdan o\'tkazish',

    'actions' => [

        'login' => [
            'before' => 'yoki',
            'label' => 'o\'z hisob raqamingiz orqali kirish',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Elektron pochta manzili',
        ],

        'name' => [
            'label' => 'Ism',
        ],

        'password' => [
            'label' => 'Parol',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Parolni tasdiqlash',
        ],

        'actions' => [

            'register' => [
                'label' => 'Roʻyxatdan oʻtish',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Roʻyxatdan oʻtishga urinishlar juda koʻp',
            'body' => 'Iltimos, :seconds soniyadan keyin qayta urinib ko\'ring.',
        ],

    ],

];
