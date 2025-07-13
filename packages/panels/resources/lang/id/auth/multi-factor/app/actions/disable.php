<?php

return [

    'label' => 'Nonaktifkan',

    'modal' => [

        'heading' => 'Nonaktifkan aplikasi authenticator',

        'description' => 'Apakah Anda yakin ingin berhenti menggunakan aplikasi Autenticator? Menonaktifkan fitur ini akan mengurangi keamanan dari akun Anda.',

        'form' => [

            'code' => [

                'label' => 'Masukkan kode 6 digit dari aplikasi authenticator',

                'validation_attribute' => 'kode',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Gunakan kode pemulihan sebagai gantinya',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Kode yang Anda masukkan tidak valid.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Atau, gunakan kode pemulihan',

                'validation_attribute' => 'kode pemulihan',

                'messages' => [

                    'invalid' => 'Kode pemulihan yang Anda masukkan tidak valid.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Nonaktifkan aplikasi authenticator',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Aplikasi authenticator telah dinonaktifkan',
        ],

    ],

];
