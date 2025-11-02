<?php

return [

    'label' => 'Set up',

    'modal' => [

        'heading' => 'Authenticator app siamna',

        'description' => <<<'BLADE'
            Google Authenticator app (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) ang chi hi hemi complete nan hian I mamawh ang.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'I authenticator app hmangin hemi QR code hi scan rawh:',

                'alt' => 'QR code to scan with an authenticator app',

            ],

            'text_code' => [

                'instruction' => 'emaw nangmahin hemi code hi enter chawp rawh:',

                'messages' => [
                    'copied' => 'Copied',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Khawngaihin heng recovery codes ho hi him takin dahtha ang che. Vawikhat chiah an rawn lang dawn a, mahse I authenticator app a access I hloh hunah I mamawh ang::',

            ],

        ],

        'form' => [

            'code' => [

                'label' => '6-digit code authenticator app ami enter rawh',

                'validation_attribute' => 'code',

                'below_content' => 'I sign in dawn ah emaw sensitive thil iti dawn anih chuan 6-digit code authenticator app ami i enter zel a ngai.',

                'messages' => [

                    'invalid' => 'Hemi code hi a diklo.',

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
            'title' => 'Authenticator app enabled ani',
        ],

    ],

];
