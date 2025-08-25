<?php

return [

    'label' => 'Profil',

    'form' => [

        'email' => [
            'label' => 'Alamat email',
        ],

        'name' => [
            'label' => 'Nama',
        ],

        'password' => [
            'label' => 'Kata sandi baru',
            'validation_attribute' => 'kata sandi',
        ],

        'password_confirmation' => [
            'label' => 'Konfirmasi kata sandi baru',
            'validation_attribute' => 'konfirmasi kata sandi',
        ],

        'current_password' => [
            'label' => 'Kata sandi saat ini',
            'below_content' => 'Untuk keamanan, harap konfirmasi kata sandi Anda untuk melanjutkan.',
            'validation_attribute' => 'kata sandi saat ini',
        ],

        'actions' => [

            'save' => [
                'label' => 'Simpan',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Autentikasi dua faktor (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Permintaan perubahan alamat email telah dikirim',
            'body' => 'Permintaan untuk mengubah alamat email Anda telah dikirim ke :email. Silakan periksa email tersebut untuk memverifikasi perubahan.',
        ],

        'saved' => [
            'title' => 'Disimpan',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Kembali',
        ],

    ],

];
