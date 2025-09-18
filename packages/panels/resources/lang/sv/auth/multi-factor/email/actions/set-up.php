<?php

return [

    'label' => 'Konfigurera',

    'modal' => [

        'heading' => 'Konfigurera mejlbaserad autentisering',

        'description' => 'Du behöver ange den 6-siffriga kod vi skickar dig via mejl varje gång du loggar in eller utför känsliga åtgärder. Kontrollera din mejl efter en 6-siffrig kod för att slutföra konfigurationen.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Aktivera mejlbaserad autentisering',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Mejlbaserad autentisering har aktiverats',
        ],

    ],

];
