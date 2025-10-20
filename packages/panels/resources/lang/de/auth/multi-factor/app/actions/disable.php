<?php

return [

    'label' => 'Ausschalten',

    'modal' => [

        'heading' => 'Authenticator-App deaktivieren',

        'description' => 'Sind Sie sicher, dass Sie die Authenticator-App nicht mehr verwenden möchten? Das Deaktivieren entfernt eine zusätzliche Sicherheitsebene von Ihrem Konto.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Authenticator-App deaktivieren',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Authenticator-App wurde deaktiviert',
        ],

    ],

];
