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
            'title' => 'Bir çox sıfırlama cəhdi',
            'body' => 'Zəhmət olmazsa :seconds saniyə sonra təkrar yoxlayın.',
        ],

    ],

];
