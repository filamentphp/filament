<?php

return [

    'label' => 'Jana semula kod pemulihan',

    'modal' => [

        'heading' => 'Jana semula kod pemulihan aplikasi authenticator',

        'description' => 'Jika anda kehilangan kod pemulihan anda, anda boleh menjana semula di sini. Kod pemulihan lama anda akan dibatalkan serta-merta.',

        'form' => [

            'code' => [

                'label' => 'Masukkan kod 6-digit dari aplikasi authenticator',

                'validation_attribute' => 'kod',

                'messages' => [

                    'invalid' => 'Kod yang anda masukkan tidak sah.',

                ],

            ],

            'password' => [

                'label' => 'Atau, masukkan kata laluan semasa anda',

                'validation_attribute' => 'kata laluan',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Jana semula kod pemulihan',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Kod pemulihan aplikasi authenticator yang baru telah dijana',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Kod pemulihan yang baru',

            'description' => 'Sila simpan kod pemulihan berikut di tempat yang selamat. Ia hanya akan ditunjukkan sekali, tetapi anda memerlukannya jika anda kehilangan akses kepada aplikasi authenticator:',

            'actions' => [

                'submit' => [
                    'label' => 'Tutup',
                ],

            ],

        ],

    ],

];
