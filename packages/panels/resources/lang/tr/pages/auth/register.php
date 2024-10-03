<?php

return [

    'title' => 'Kayıt Ol',

    'heading' => 'Üye Ol',

    'actions' => [

        'login' => [
            'before' => 'veya',
            'label' => 'hesabınıza giriş yapın',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-posta adresi',
        ],

        'name' => [
            'label' => 'Ad',
        ],

        'password' => [
            'label' => 'Şifre',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Şifreyi onayla',
        ],

        'actions' => [

            'register' => [
                'label' => 'Üye ol',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Çok fazla kayıt denemesi',
            'body' => 'Lütfen :seconds saniye sonra tekrar deneyin.',
        ],

    ],

];
