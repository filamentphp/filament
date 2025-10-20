<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'Alamat emel',
        ],

        'name' => [
            'label' => 'Nama',
        ],

        'password' => [
            'label' => 'Kata laluan baru',
            'validation_attribute' => 'kata laluan',
        ],

        'password_confirmation' => [
            'label' => 'Sahkan kata laluan baharu',
            'validation_attribute' => 'pengesahan kata laluan',
        ],

        'current_password' => [
            'label' => 'Kata laluan semasa',
            'below_content' => 'Untuk keselamatan, sila sahkan kata laluan anda untuk meneruskan.',
            'validation_attribute' => 'kata laluan semasa',
        ],

        'actions' => [

            'save' => [
                'label' => 'Simpan perubahan',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Pengesahan dua faktor (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Permintaan perubahan alamat emel telah dihantar',
            'body' => 'Permintaan untuk mengubah alamat emel anda telah dihantar ke :email. Sila semak emel anda untuk mengesahkan perubahan tersebut.',
        ],

        'saved' => [
            'title' => 'Disimpan',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Batal',
        ],

    ],

];
