<?php

return [

    'label' => 'Itzali',

    'modal' => [

        'heading' => 'Autentifikazio-aplikazioa desgaitu',

        'description' => 'Ziur zaude autentifikazio-aplikazioa erabiltzeari utzi nahi diozula? Hau desgaitzeak segurtasun-geruza gehigarri bat kenduko du zure kontutik.',

        'form' => [

            'code' => [

                'label' => 'Sartu autentifikazio-aplikazioko 6 digituko kodea',

                'validation_attribute' => 'kodea',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Erabili berreskuratze-kode bat horren ordez',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Sartu duzun kodea ez da baliozkoa.',

                    'rate_limited' => 'Saiakera gehiegi. Mesedez, saiatu berriro geroago.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Edo, sartu berreskuratze-kode bat',

                'validation_attribute' => 'berreskuratze-kodea',

                'messages' => [

                    'invalid' => 'Sartu duzun berreskuratze-kodea ez da baliozkoa.',

                    'rate_limited' => 'Saiakera gehiegi. Mesedez, saiatu berriro geroago.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Autentifikazio-aplikazioa desgaitu',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Autentifikazio-aplikazioa desgaitu da',
        ],

    ],

];
