<?php

return [

    'title' => 'Giriş Et',

    'heading' => 'Daxil Ol',

    'actions' => [

        'register' => [
            'before' => 'və ya',
            'label' => 'hesab yaradın',
        ],

        'request_password_reset' => [
            'label' => 'Şifrənizi unutmusunuz?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-poçt ünvanı',
        ],

        'password' => [
            'label' => 'Şifrə',
        ],

        'remember' => [
            'label' => 'Məni Xatırla',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Giriş Et',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Daxil etdiyiniz məlumatlara uyğun hesab tapılmadı.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Bir çox giriş cəhdi',
            'body' => 'Zəhmət olmazsa :seconds saniyə sonra təkrar yoxlayın.',
        ],

    ],

];
