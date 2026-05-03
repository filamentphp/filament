<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'E-posti kinnituskoodid',

            'below_content' => 'Saage ajutine kood oma e-posti aadressile, et kinnitada oma identiteeti sisselogimise ajal.',

            'messages' => [
                'enabled' => 'Lubatud',
                'disabled' => 'Keelatud',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Saada kood oma e-posti aadressile',

        'code' => [

            'label' => 'Sisestage 6-kohaline kood, mille me teile e-posti teel saatsime',

            'validation_attribute' => 'kood',

            'actions' => [

                'resend' => [

                    'label' => 'Saada uus kood e-posti teel',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Oleme saatnud teile uue koodi e-posti teel',
                        ],

                        'throttled' => [
                            'title' => 'Liiga palju uuesti saatmise katseid. Palun oodake enne uue koodi küsimist.',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Sisestatud kood on vale.',

            ],

        ],

    ],

];
