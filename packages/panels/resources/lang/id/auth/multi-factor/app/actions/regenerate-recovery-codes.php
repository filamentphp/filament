<?php

return [

    'label' => 'Buat ulang kode pemulihan',

    'modal' => [

        'heading' => 'Buat ulang kode pemulihat aplikasi authenticator',

        'description' => 'Jika Anda kehilangan kode pemulihan, Anda dapat membuat ulang di sini. Kode pemulihan lama akan langsung dinonaktifkan.',

        'form' => [

            'code' => [

                'label' => 'Masukkan kode 6 digit dari aplikasi authenticator',

                'validation_attribute' => 'kode',

                'messages' => [

                    'invalid' => 'Kode yang Anda masukkan tidak valid.',

                ],

            ],

            'password' => [

                'label' => 'Atau, gunakan kata sandi',

                'validation_attribute' => 'kata sandi',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Buat ulang kode pemulihan',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Kode pemulihan baru untuk aplikasi authenticator berhasil dibuat',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Kode pemulihan baru',

            'description' => 'Harap simpan kode pemulihan berikut pada tempat yang aman. Kode ini hanya akan ditampilkan sekali, tetapi akan diperlukan jika Anda kehilangan akses ke aplikasi authenticator anda:',

            'actions' => [

                'submit' => [
                    'label' => 'Tutup',
                ],

            ],

        ],

    ],

];
