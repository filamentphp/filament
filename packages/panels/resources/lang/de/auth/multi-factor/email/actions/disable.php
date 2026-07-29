<?php

return [

    'label' => 'Ausschalten',

    'modal' => [

        'heading' => 'E-Mail-Bestätigungscodes deaktivieren',

        'description' => 'Sind Sie sicher, dass Sie keine E-Mail-Bestätigungscodes mehr erhalten möchten? Das Deaktivieren entfernt eine zusätzliche Sicherheitsebene von Ihrem Konto.',

        'form' => [

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

                    'rate_limited' => 'Zu viele Versuche. Bitte versuchen Sie es später erneut.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'E-Mail-Bestätigungscodes deaktivieren',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'E-Mail-Bestätigungscodes wurden deaktiviert',
        ],

    ],

];
