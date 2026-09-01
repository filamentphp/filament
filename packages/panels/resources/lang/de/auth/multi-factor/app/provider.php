<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Authenticator-App',

            'below_content' => 'Verwenden Sie eine sichere App, um einen temporären Code für die Anmeldeverifizierung zu generieren.',

            'messages' => [
                'enabled' => 'Aktiviert',
                'disabled' => 'Deaktiviert',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Verwenden Sie einen Code aus Ihrer Authenticator-App',

        'code' => [

            'label' => 'Geben Sie den 6-stelligen Code aus der Authenticator-App ein',

            'validation_attribute' => 'Code',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Stattdessen einen Wiederherstellungscode verwenden',
                ],

            ],

            'messages' => [

                'invalid' => 'Der eingegebene Code ist ungültig.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Oder geben Sie einen Wiederherstellungscode ein',

            'validation_attribute' => 'Wiederherstellungscode',

            'messages' => [

                'invalid' => 'Der eingegebene Wiederherstellungscode ist ungültig.',

            ],

        ],

    ],

];
