<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Authenticator-app',

            'below_content' => 'Gebruik een beveiligde app om een tijdelijke code te genereren voor loginverificatie.',

            'messages' => [
                'enabled' => 'Ingeschakeld',
                'disabled' => 'Uitgeschakeld',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Gebruik een code uit je authenticator-app',

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

];
