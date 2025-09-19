<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Mejlbaserad autentisering',

            'below_content' => 'Ta emot en tillfällig kod på din mejladress för att verifiera din identitet vid inloggning.',

            'messages' => [
                'enabled' => 'Aktiverad',
                'disabled' => 'Inaktiverad',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Skicka en kod till din mejl',

        'code' => [

            'label' => 'Ange den 6-siffriga kod vi skickade till dig via mejl',

            'validation_attribute' => 'kod',

            'actions' => [

                'resend' => [

                    'label' => 'Skicka en ny kod via mejl',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Vi har skickat dig en ny kod via mejl',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Koden du angav är ogiltig.',

            ],

        ],

    ],

];
