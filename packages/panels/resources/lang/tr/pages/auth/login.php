<?php

return [

    'title' => 'Giriş yap',

    'heading' => 'Hesabınıza giriş yapın',

    'form' => [

        'email' => [
            'label' => 'E-posta adresi',
        ],

        'password' => [
            'label' => 'Parola',
        ],

        'remember' => [
            'label' => 'Beni hatırla',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Giriş yap',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Bu kimlik bilgileri kayıtlarla eşleşmiyor.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Çok fazla giriş denemesi. Lütfen :seconds saniye sonra tekrar deneyin.',
        ],

    ],

];
