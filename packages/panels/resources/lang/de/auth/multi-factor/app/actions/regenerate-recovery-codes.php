<?php

return [

    'label' => 'Wiederherstellungscodes neu generieren',

    'modal' => [

        'heading' => 'Wiederherstellungscodes der Authenticator-App neu generieren',

        'description' => 'Wenn Sie Ihre Wiederherstellungscodes verlieren, können Sie sie hier neu generieren. Ihre alten Wiederherstellungscodes werden sofort ungültig.',

        'form' => [

            'code' => [

                'label' => 'Geben Sie den 6-stelligen Code aus der Authenticator-App ein',

                'validation_attribute' => 'Code',

                'messages' => [

                    'invalid' => 'Der eingegebene Code ist ungültig.',

                ],

            ],

            'password' => [

                'label' => 'Oder geben Sie Ihr aktuelles Passwort ein',

                'validation_attribute' => 'Passwort',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Wiederherstellungscodes neu generieren',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Neue Wiederherstellungscodes der Authenticator-App wurden generiert',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Neue Wiederherstellungscodes',

            'description' => 'Bitte speichern Sie die folgenden Wiederherstellungscodes an einem sicheren Ort. Sie werden nur einmal angezeigt, aber Sie benötigen sie, wenn Sie den Zugang zu Ihrer Authenticator-App verlieren:',

            'actions' => [

                'submit' => [
                    'label' => 'Schließen',
                ],

            ],

        ],

    ],

];
