<?php

return [

    'label' => 'Herstelcodes opnieuw genereren',

    'modal' => [

        'heading' => 'Herstelcodes voor authenticator-app opnieuw genereren',

        'description' => 'Als je je herstelcodes kwijtraakt, kun je ze hier opnieuw genereren. Je oude herstelcodes worden onmiddellijk ongeldig.',

        'form' => [

            'code' => [

                'label' => 'Voer de 6-cijferige code uit de authenticator-app in',

                'validation_attribute' => 'code',

                'messages' => [

                    'invalid' => 'De ingevoerde code is ongeldig.',

                ],

            ],

            'password' => [

                'label' => 'Of voer je huidige wachtwoord in',

                'validation_attribute' => 'wachtwoord',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Herstelcodes opnieuw genereren',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Nieuwe herstelcodes voor de authenticator-app zijn gegenereerd',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Nieuwe herstelcodes',

            'description' => 'Bewaar de volgende herstelcodes op een veilige plek. Ze worden maar één keer getoond, maar je hebt ze nodig als je de toegang tot je authenticator-app verliest:',

            'actions' => [

                'submit' => [
                    'label' => 'Sluiten',
                ],

            ],

        ],

    ],

];
