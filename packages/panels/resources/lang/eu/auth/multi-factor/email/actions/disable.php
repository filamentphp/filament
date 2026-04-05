<?php

return [

    'label' => 'Itzali',

    'modal' => [

        'heading' => 'Posta elektronikoko egiaztapen-kodeak desgaitu',

        'description' => 'Ziur zaude posta elektronikoko egiaztapen-kodeak jasotzeari utzi nahi diozula? Hau desgaitzeak segurtasun-geruza gehigarri bat kenduko du zure kontutik.',

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
                'label' => 'Posta elektronikoko egiaztapen-kodeak desgaitu',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Posta elektronikoko egiaztapen-kodeak desgaitu dira',
        ],

    ],

];
