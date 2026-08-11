<?php

return [

    'label' => 'I-set up',

    'modal' => [

        'heading' => 'I-set up ang authenticator app',

        'description' => <<<'BLADE'
            Kakailanganin mo ng app tulad ng Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) para makumpleto ang prosesong ito.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'I-scan ang QR code na ito gamit ang iyong authenticator app:',

                'alt' => 'QR code na i-scan gamit ang authenticator app',

            ],

            'text_code' => [

                'instruction' => 'O ilagay nang mano-mano ang code na ito:',

                'messages' => [
                    'copied' => 'Nakopya',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'I-save ang mga sumusunod na recovery code sa ligtas na lugar. Isang beses lang ipapakita ang mga ito, pero kakailanganin mo ang mga ito kung mawalan ka ng access sa iyong authenticator app:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Ilagay ang 6-digit code mula sa authenticator app',

                'below_content' => 'Kakailanganin mong ilagay ang 6-digit code mula sa iyong authenticator app sa tuwing magsa-sign in ka o gagawa ng sensitibong action.',

                'messages' => [

                    'invalid' => 'Invalid ang code na inilagay mo.',

                    'rate_limited' => 'Masyadong maraming subok. Subukan ulit mamaya.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'I-enable ang authenticator app',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Na-enable ang authenticator app',
        ],

    ],

];
