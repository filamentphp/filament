<?php

return [

    'title' => 'Tizimga kirish',

    'heading' => 'Hisobingizga kiring',

    'actions' => [

        'register' => [
            'before' => 'yoki',
            'label' => 'hisob qaydnomasini ro\'yxatdan o\'tkazing',
        ],

        'request_password_reset' => [
            'label' => 'Parolni unutdingizmi?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Elektron pochta manzili',
        ],

        'password' => [
            'label' => 'Parol',
        ],

        'remember' => [
            'label' => 'Meni eslab qol',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Hisob qaydnomasiga kirish',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Siz kiritgan foydalanuvchi nomi yoki parol noto\'g\'ri.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Kirish uchun urinishlar soni juda koʻp',
            'body' => 'Iltimos, :seconds soniyadan keyin qayta urinib ko\'ring.',
        ],

    ],

];
