<?php

return [

    'label' => 'Ota käyttöön',

    'modal' => [

        'heading' => 'Ota todennussovellus käyttöön',

        'description' => <<<'BLADE'
            Tarvitset sovelluksen kuten Microsoft Authenticator (<x-filament::link href="https://apps.apple.com/fi/app/microsoft-authenticator/id983156458?l=fi" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.azure.authenticator&hl=fi" target="_blank">Android</x-filament::link>) käyttöönottoa varten.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Skannaa tämä QR-koodi todennussovelluksella:',

                'alt' => 'QR-koodi todennussovelluksella skannausta varten',

            ],

            'text_code' => [

                'instruction' => 'Tai syötä tämä koodi käsin:',

                'messages' => [
                    'copied' => 'Kopioitu',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Please save the following recovery codes in a safe place. They will only be shown once, but you\'ll need them if you lose access to your authenticator app:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Syötä todennussovelluksen antama koodi',

                'validation_attribute' => 'code',

                'below_content' => 'You will need to enter the 6-digit code from your authenticator app each time you sign in or perform sensitive actions.',

                'messages' => [

                    'invalid' => 'The code you entered is invalid.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Enable authenticator app',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Todennussovellus on otettu käyttöön',
        ],

    ],

];
