<?php

return [

    'label' => 'Konfiguratu',

    'modal' => [

        'heading' => 'Posta elektronikoko egiaztapen-kodeak konfiguratu',

        'description' => 'Saioa hasten duzun edo ekintza sentikorrak egiten dituzun bakoitzean, posta elektronikoz bidalitako 6 digituko kodea sartu beharko duzu. Begiratu zure posta elektronikoa konfigurazioa osatzeko 6 digituko kodea lortzeko.',

        'form' => [

            'code' => [

                'label' => 'Sartu posta elektronikoz bidalitako 6 digituko kodea',

                'validation_attribute' => 'kodea',

                'actions' => [

                    'resend' => [

                        'label' => 'Kode berria bidali posta elektronikoz',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Kode berria bidali dizugu posta elektronikoz',
                            ],

                            'throttled' => [
                                'title' => 'Birbidalketa-saiakera gehiegi. Mesedez, itxaron beste kode bat eskatu aurretik.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Sartu duzun kodea ez da baliozkoa.',

                    'rate_limited' => 'Saiakera gehiegi. Mesedez, saiatu berriro geroago.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Posta elektronikoko egiaztapen-kodeak gaitu',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Posta elektronikoko egiaztapen-kodeak gaitu dira',
        ],

    ],

];
