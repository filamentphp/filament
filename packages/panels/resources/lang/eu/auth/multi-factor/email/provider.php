<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Posta elektronikoko egiaztapen-kodeak',

            'below_content' => 'Jaso aldi baterako kodea zure helbide elektronikoan zure identitatea egiaztatzeko saio-hasieran.',

            'messages' => [
                'enabled' => 'Gaituta',
                'disabled' => 'Desgaituta',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Bidali kodea zure posta elektronikora',

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

            ],

        ],

    ],

];
