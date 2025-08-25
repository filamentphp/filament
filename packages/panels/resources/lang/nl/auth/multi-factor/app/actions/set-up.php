<?php

return [

    'label' => 'Instellen',

    'modal' => [

        'heading' => 'Authenticator-app instellen',

        'description' => <<<'BLADE'
            Je hebt een app zoals Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) nodig om dit proces te voltooien.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Scan deze QR-code met je authenticator-app:',

                'alt' => 'QR-code om te scannen met een authenticator-app',

            ],

            'text_code' => [

                'instruction' => 'Of voer deze code handmatig in:',

                'messages' => [
                    'copied' => 'Gekopieerd',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Bewaar de volgende herstelcodes op een veilige plek. Ze worden maar één keer getoond, maar je hebt ze nodig als je de toegang tot je authenticator-app verliest:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Voer de 6-cijferige code uit de authenticator-app in',

                'validation_attribute' => 'code',

                'below_content' => 'Je moet de 6-cijferige code van je authenticator-app invoeren telkens als je inlogt of gevoelige acties uitvoert.',

                'messages' => [

                    'invalid' => 'De ingevoerde code is ongeldig.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Authenticator-app inschakelen',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Authenticator-app is ingeschakeld',
        ],

    ],

];
