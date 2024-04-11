<?php

return [

    'title' => 'Şifrenizi sıfırlayın',

    'heading' => 'Şifrenizi sıfırlayın',

    'form' => [

        'email' => [
            'label' => 'E-posta adresi',
        ],

        'password' => [
            'label' => 'Şifre',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Şifreyi onayla',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Şifreyi sıfırla',
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
