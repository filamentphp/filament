<?php

return [

    'title' => 'Şifrenizi Sıfırlayın',

    'heading' => 'Şifrenizi Sıfırlayın',

    'form' => [

        'email' => [
            'label' => 'E-posta adresi',
        ],

        'password' => [
            'label' => 'Şifre',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Şifreyi Onayla',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Şifreyi Sıfırla',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Çok fazla sıfırlama denemesi',
            'body' => 'Lütfen :seconds saniye sonra tekrar deneyin.',
        ],

    ],

];
