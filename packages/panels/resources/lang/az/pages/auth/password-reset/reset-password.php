<?php

return [

    'title' => 'Şifrənizi Sıfırlayın',

    'heading' => 'Şifrənizi Sıfırlayın',

    'form' => [

        'email' => [
            'label' => 'E-poçt ünvanı',
        ],

        'password' => [
            'label' => 'Şifrə',
            'validation_attribute' => 'şifrə',
        ],

        'password_confirmation' => [
            'label' => 'Şifrəni Təsdiqlə',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Şifrəni Sıfırla',
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
