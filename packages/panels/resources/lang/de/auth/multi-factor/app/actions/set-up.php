<?php

return [

    'label' => 'Einrichten',

    'modal' => [

        'heading' => 'Authenticator-App einrichten',

        'description' => <<<'BLADE'
            Sie benötigen eine App wie Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>), um diesen Vorgang abzuschließen.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Scannen Sie diesen QR-Code mit Ihrer Authenticator-App:',

                'alt' => 'QR-Code zum Scannen mit einer Authenticator-App',

            ],

            'text_code' => [

                'instruction' => 'Oder geben Sie diesen Code manuell ein:',

                'messages' => [
                    'copied' => 'Kopiert',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Bitte speichern Sie die folgenden Wiederherstellungscodes an einem sicheren Ort. Sie werden nur einmal angezeigt, aber Sie benötigen sie, wenn Sie den Zugang zu Ihrer Authenticator-App verlieren:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Geben Sie den 6-stelligen Code aus der Authenticator-App ein',

                'validation_attribute' => 'Code',

                'below_content' => 'Sie müssen bei jeder Anmeldung oder beim Ausführen sensibler Aktionen den 6-stelligen Code aus Ihrer Authenticator-App eingeben.',

                'messages' => [

                    'invalid' => 'Der eingegebene Code ist ungültig.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Authenticator-App aktivieren',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Authenticator-App wurde aktiviert',
        ],

    ],

];
