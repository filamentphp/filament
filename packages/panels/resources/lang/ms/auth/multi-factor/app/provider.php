<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Authenticator app',

            'below_content' => 'Gunakan aplikasi yang selamat untuk menghasilkan kod sementara bagi pengesahan log masuk.',

            'messages' => [
                'enabled' => 'Diaktifkan',
                'disabled' => 'Dinyahaktifkan',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Gunakan kod dari aplikasi authenticator',

        'code' => [

            'label' => 'Masukkan kod 6-digit dari aplikasi authenticator',

            'validation_attribute' => 'kod',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Gunakan kod pemulihan sebagai ganti',
                ],

            ],

            'messages' => [

                'invalid' => 'Kod yang anda masukkan tidak sah.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Atau, masukkan kod pemulihan',

            'validation_attribute' => 'kod pemulihan',

            'messages' => [

                'invalid' => 'Kod pemulihan yang anda masukkan tidak sah.',

            ],

        ],

    ],

];
