<?php

return [

    'label' => 'Isključi',

    'modal' => [

        'heading' => 'Isključivanje aplikacije za autentifikaciju',

        'description' => 'Da li ste sigurni da želite da isključite aplikaciju za autentifikaciju? Isključivanje će ukloniti dodatni nivo zaštite vašeg naloga.',

        'form' => [

            'code' => [

                'label' => 'Unesite kod od 6 cifara iz aplikacije za autentifikaciju',

                'validation_attribute' => 'kod',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Koristite kod za oporavak',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Kod koji ste uneli nije ispravan.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Ili unesite kod za oporavak',

                'validation_attribute' => 'kod za oporavak',

                'messages' => [

                    'invalid' => 'Kod za oporavak koji ste uneli nije ispravan.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Isključi aplikaciju za autentifikaciju',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Aplikacija za autentifikaciju je isključena',
        ],

    ],

];
