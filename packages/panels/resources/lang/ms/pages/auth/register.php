<?php

return [

    'title' => 'Daftar',

    'heading' => 'Daftar',

    'actions' => [

        'login' => [
            'before' => 'atau',
            'label' => 'log masuk ke akaun anda',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Alamat emel',
        ],

        'name' => [
            'label' => 'Nama',
        ],

        'password' => [
            'label' => 'Kata laluan',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Sahkan kata laluan',
        ],

        'actions' => [

            'register' => [
                'label' => 'Daftar',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Terlalu banyak percubaan pendaftaran',
            'body' => 'Sila cuba lagi dalam :second saat.',
        ],

    ],

];
