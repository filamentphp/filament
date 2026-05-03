<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Autentimise rakendus',

            'below_content' => 'Kasutage turvalist rakendust, et genereerida ajutine kood sisselogimise kinnitamiseks.',

            'messages' => [
                'enabled' => 'Lubatud',
                'disabled' => 'Keelatud',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Kasutage koodi oma autentimise rakendusest',

        'code' => [

            'label' => 'Sisestage autentimise rakendusest saadud 6-kohaline kood',

            'validation_attribute' => 'kood',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Kasutage selle asemel taastamiskoodi',
                ],

            ],

            'messages' => [

                'invalid' => 'Sisestatud kood on vale.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Või sisestage taastamiskood',

            'validation_attribute' => 'taastamiskood',

            'messages' => [

                'invalid' => 'Sisestatud taastamiskood on vale.',

            ],

        ],

    ],

];
