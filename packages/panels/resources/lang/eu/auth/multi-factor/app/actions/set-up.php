<?php

return [

    'label' => 'Konfiguratu',

    'modal' => [

        'heading' => 'Autentifikazio-aplikazioa konfiguratu',

        'description' => <<<'BLADE'
            Google Authenticator bezalako aplikazio bat beharko duzu (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) prozesu hau osatzeko.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Eskaneatu QR kode hau zure autentifikazio-aplikazioarekin:',

                'alt' => 'Autentifikazio-aplikazio batekin eskaneatzeko QR kodea',

            ],

            'text_code' => [

                'instruction' => 'Edo sartu kode hau eskuz:',

                'messages' => [
                    'copied' => 'Kopiatuta',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Mesedez, gorde ondorengo berreskuratze-kodeak leku seguru batean. Behin bakarrik erakutsiko dira, baina behar izango dituzu autentifikazio-aplikaziorako sarbidea galtzen baduzu:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Sartu autentifikazio-aplikazioko 6 digituko kodea',

                'validation_attribute' => 'kodea',

                'below_content' => 'Saioa hasten duzun edo ekintza sentikorrak egiten dituzun bakoitzean, autentifikazio-aplikazioko 6 digituko kodea sartu beharko duzu.',

                'messages' => [

                    'invalid' => 'Sartu duzun kodea ez da baliozkoa.',

                    'rate_limited' => 'Saiakera gehiegi. Mesedez, saiatu berriro geroago.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Autentifikazio-aplikazioa gaitu',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Autentifikazio-aplikazioa gaitu da',
        ],

    ],

];
