<?php

return [

    'label' => 'Set up',

    'modal' => [

        'heading' => 'Set up authenticator app',

        'description' => <<<'BLADE'
            You'll need an app like Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) to complete this process.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Scan this QR code with your authenticator app:',

                'alt' => 'QR code to scan with an authenticator app',

            ],

            'text_code' => [

                'instruction' => 'Or enter this code manually:',

                'messages' => [
                    'copied' => 'Copied',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Please save the following recovery codes in a safe place. They will only be shown once, but you\'ll need them if you lose access to your authenticator app:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Enter the 6-digit code from the authenticator app',

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
            'title' => 'Authenticator app has been enabled',
        ],

    ],

];
