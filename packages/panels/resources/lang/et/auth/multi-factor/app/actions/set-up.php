<?php

return [

    'label' => 'Seadistamine',

    'modal' => [

        'heading' => 'Seadistage autentimise rakendus',

        'description' => <<<'BLADE'
            Teil on vaja rakendust nagu Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>), et see protsess lõpule viia.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Skaneerige see QR-kood oma autentimise rakendusega:',

                'alt' => 'QR-kood, mida skaneerida autentimise rakendusega',

            ],

            'text_code' => [

                'instruction' => 'Või sisestage see kood käsitsi:',

                'messages' => [
                    'copied' => 'Kopeeritud',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Palun salvestage järgmised taastamiskoodid turvalisse kohta. Need kuvatakse ainult üks kord, kuid vajate neid, kui kaotate juurdepääsu oma autentimise rakendusele:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Sisestage autentimise rakendusest saadud 6-kohaline kood',

                'validation_attribute' => 'kood',

                'below_content' => 'Iga kord, kui sisse logite või teete tundlikke toiminguid, peate sisestama autentimise rakendusest saadud 6-kohalise koodi.',

                'messages' => [

                    'invalid' => 'Sisestatud kood on vale.',

                    'rate_limited' => 'Liiga palju katseid. Palun proovige hiljem uuesti.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Luba autentimise rakendus',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Autentimise rakendus on lubatud',
        ],

    ],

];
