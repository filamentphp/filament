<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'E-Mail-Verifizierungscodes',

            'below_content' => 'Erhalten Sie einen temporären Code an Ihre E-Mail-Adresse, um Ihre Identität bei der Anmeldung zu verifizieren.',

            'messages' => [
                'enabled' => 'Aktiviert',
                'disabled' => 'Deaktiviert',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Code an Ihre E-Mail-Adresse senden',

        'code' => [

            'label' => 'Geben Sie den 6-stelligen Code ein, den wir Ihnen per E-Mail gesendet haben',

            'validation_attribute' => 'Code',

            'actions' => [

                'resend' => [

                    'label' => 'Neuen Code per E-Mail senden',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Wir haben Ihnen einen neuen Code per E-Mail gesendet',
                        ],

                        'throttled' => [
                            'title' => 'Zu viele Sendeversuche. Bitte warten Sie, bevor Sie einen neuen Code anfordern.',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Der eingegebene Code ist ungültig.',

            ],

        ],

    ],

];
