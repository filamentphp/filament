<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Aplikasi authenticator',

            'below_content' => 'Gunakan aplikasi yang aman untuk membuat kode sementara untuk verifikasi saat login.',

            'messages' => [
                'enabled' => 'Aktif',
                'disabled' => 'Nonaktif',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Gunakan kode dari aplikasi authenticator Anda',

        'code' => [

            'label' => 'Masukkan kode 6 digit dari aplikasi authenticatorp',

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

];
