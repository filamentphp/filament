<?php

return [

    'label' => 'Configurează',

    'modal' => [

        'heading' => 'Configurare aplicație de autentificare',

        'description' => <<<'BLADE'
            Veți avea nevoie de o aplicație precum Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) pentru a finaliza acest proces.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Scanați acest cod QR cu aplicația de autentificare:',

                'alt' => 'Cod QR de scanat cu o aplicație de autentificare',

            ],

            'text_code' => [

                'instruction' => 'Sau introduceți acest cod manual:',

                'messages' => [
                    'copied' => 'Copiat',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Vă rugăm să salvați următoarele coduri de recuperare într-un loc sigur. Acestea vor fi afișate doar o singură dată, dar veți avea nevoie de ele dacă pierdeți accesul la aplicația de autentificare:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Introduceți codul din 6 cifre din aplicația de autentificare',

                'validation_attribute' => 'cod',

                'below_content' => 'Va trebui să introduceți codul din 6 cifre din aplicația de autentificare de fiecare dată când vă autentificați sau efectuați acțiuni sensibile.',

                'messages' => [

                    'invalid' => 'Codul introdus este invalid.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Activează aplicația de autentificare',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Aplicația de autentificare a fost activată',
        ],

    ],

];
