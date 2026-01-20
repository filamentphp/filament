<?php

return [

    'label' => 'Käyttöönotto',

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

                'instruction' => 'Tallenna seuraavat palautuskoodit turvalliseen paikkaan. Koodit näytetään vain kerran ja tarvitset niitä jos menetät pääsyn todennussovellukseen:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Syötä todennussovelluksen antama koodi',

                'validation_attribute' => 'koodi',

                'below_content' => 'Sinun tulee syöttää 6-merkkinen koodi todennussovelluksesta joka kerta kun kirjaudut tai teet arkaluonteisia toimintoja.',

                'messages' => [

                    'invalid' => 'Antamasi koodi on viallinen.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Ota todennussovellus käyttöön',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Todennussovellus on otettu käyttöön',
        ],

    ],

];
