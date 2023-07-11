<?php

return [

    'title' => 'Masuk',

    'heading' => 'Masuk ke akun Anda',

    'form' => [

        'email' => [
            'label' => 'Alamat Email',
        ],

        'password' => [
            'label' => 'Kata sandi',
        ],

        'remember' => [
            'label' => 'Ingat saya',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Masuk',
            ],

        ],

    ],

    'messages' => [
        'failed' => 'Kredensial yang diberikan tidak dapat ditemukan.',
        'throttled' => 'Terlalu banyak percobaan masuk. Silakan ulangi dalam :seconds detik.',
    ],

];
