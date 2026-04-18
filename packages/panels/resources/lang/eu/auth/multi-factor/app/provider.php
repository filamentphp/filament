<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Autentifikazio-aplikazioa',

            'below_content' => 'Erabili aplikazio seguru bat saio-hasieraren egiaztapenerako aldi baterako kodea sortzeko.',

            'messages' => [
                'enabled' => 'Gaituta',
                'disabled' => 'Desgaituta',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Erabili autentifikazio-aplikazioko kodea',

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

            ],

        ],

        'recovery_code' => [

            'label' => 'Edo, sartu berreskuratze-kode bat',

            'validation_attribute' => 'berreskuratze-kodea',

            'messages' => [

                'invalid' => 'Sartu duzun berreskuratze-kodea ez da baliozkoa.',

            ],

        ],

    ],

];
