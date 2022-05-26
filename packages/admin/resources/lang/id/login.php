<?php

return [

    'title' => 'Login',

    'heading' => 'Masuk ke akun Anda',

    'buttons' => [

        'submit' => [
            'label' => 'Masuk',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Alamat Email',
        ],

        'password' => [
            'label' => 'Kata sandi',
        ],

        'remember' => [
            'label' => 'Ingat Saya',
        ],

    ],

    'messages' => [
        'failed' => 'Kredensial yang diberikan tidak dapat ditemukan.',
        'throttled' => 'Terlalu banyak mencoba masuk. Silahkan ulangi dalam :seconds detik.',
    ],

];
