<?php

return [

    'label' => 'Stäng av',

    'modal' => [

        'heading' => 'Inaktivera mejlbaserad autentisering',

        'description' => 'Är du säker på att du vill stänga av mejlbaserad autentisering? Att inaktivera detta tar bort ett extra säkerhetsskikt från ditt konto.',

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

                            'throttled' => [
                                'title' => 'För många försök inom begränsad tidsintervall. Vänta innan du begär en ny kod.',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Koden du angav är ogiltig.',

                    'rate_limited' => 'För många försök inom begränsad tidsintervall. Vänligen försök igen senare.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Inaktivera mejlbaserad autentisering',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Mejlbaserad autentisering har inaktiverats',
        ],

    ],

];
