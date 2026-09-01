<?php

return [

    'label' => 'Postavka',

    'modal' => [

        'heading' => 'Postavka aplikacije za autentifikaciju',

        'description' => <<<'BLADE'
            Neophodna je aplikacija za autentifikaciju poput Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) da biste nastavili.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Skenirajte QR kod pomoću aplikacije za autentifikaciju:',

                'alt' => 'QR za autentifikaciju',

            ],

            'text_code' => [

                'instruction' => 'Ili ručno unesite ovaj kod:',

                'messages' => [
                    'copied' => 'Kopirano',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Čuvajte ove kodove za oporavak na bezbednom mestu. Oni će biti prikazani samo jednom, ali će biti neophodni ako izgubite pristup aplikaciji za autentifikaciju:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Unesite kod od 6 cifara iz aplikacije za autentifikaciju',

                'validation_attribute' => 'kod',

                'below_content' => 'Potrebno je uneti kod od 6 cifara iz aplikacije za autentifikaciju svaki put kad se prijavljujete ili izvršavate osetljive akcije.',

                'messages' => [

                    'invalid' => 'Kod koji ste uneli nije ispravan.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Omogući aplikaciju za autentifikaciju',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Aplikacije za autentifikaciju je omogućena',
        ],

    ],

];
