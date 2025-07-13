<?php

return [

    'label' => 'Einrichten',

    'modal' => [

        'heading' => 'E-Mail-Bestätigungscodes einrichten',

        'description' => 'Sie müssen bei jeder Anmeldung oder beim Ausführen sensibler Aktionen den 6-stelligen Code eingeben, den wir Ihnen per E-Mail senden. Überprüfen Sie Ihre E-Mails auf einen 6-stelligen Code, um die Einrichtung abzuschließen.',

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

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Der eingegebene Code ist ungültig.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'E-Mail-Bestätigungscodes aktivieren',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'E-Mail-Bestätigungscodes wurden aktiviert',
        ],

    ],

];
