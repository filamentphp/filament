<?php

return [

    'title' => 'Qeydiyyatdan keç',

    'heading' => 'Hesab Yarat',

    'actions' => [

        'login' => [
            'before' => 'və ya',
            'label' => 'hesabınıza giriş edin',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-poçt ünvanı',
        ],

        'name' => [
            'label' => 'Ad',
        ],

        'password' => [
            'label' => 'Şifrə',
            'validation_attribute' => 'şifrə',
        ],

        'password_confirmation' => [
            'label' => 'Şifreni Təsdiqlə',
        ],

        'actions' => [

            'register' => [
                'label' => 'Hesab Yarat',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Bir çox hesab yaratma cəhdi',
            'body' => 'Zəhmət olmazsa :seconds saniyə sonra təkrar yoxlayın.',
        ],

    ],

];
