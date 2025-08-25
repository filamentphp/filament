<?php

return [

    'label' => 'Uitschakelen',

    'modal' => [

        'heading' => 'Authenticator-app uitschakelen',

        'description' => 'Weet je zeker dat je wilt stoppen met het gebruik van de authenticator-app? Door dit uit te schakelen, verwijder je een extra beveiligingslaag van je account.',

        'form' => [

            'code' => [

                'label' => 'Voer de 6-cijferige code uit de authenticator-app in',

                'validation_attribute' => 'code',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Gebruik in plaats daarvan een herstelcode',
                    ],

                ],

                'messages' => [

                    'invalid' => 'De ingevoerde code is ongeldig.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Of voer een herstelcode in',

                'validation_attribute' => 'herstelcode',

                'messages' => [

                    'invalid' => 'De ingevoerde herstelcode is ongeldig.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Authenticator-app uitschakelen',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Authenticator-app is uitgeschakeld',
        ],

    ],

];
