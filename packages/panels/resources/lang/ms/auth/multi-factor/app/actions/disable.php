<?php

return [

    'label' => 'Matikan',

    'modal' => [

        'heading' => 'Nyahaktifkan aplikasi authenticator',

        'description' => 'Adakah anda pasti ingin berhenti menggunakan aplikasi authenticator? Menyahaktifkan ini akan mengeluarkan lapisan keselamatan tambahan dari akaun anda.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Nyahaktifkan aplikasi authenticator',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Aplikasi authenticator telah dinyahaktifkan',
        ],

    ],

];
