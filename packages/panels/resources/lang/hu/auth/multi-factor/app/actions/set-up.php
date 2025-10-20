<?php

return [

    'label' => 'Bekapcsolás',

    'modal' => [

        'heading' => 'Hitelesítő alkalmazás bekapcsolása',

        'description' => <<<'BLADE'
            Egy alkalmazásra lesz szükséged, mint a Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) a folyamat befejezéséhez.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Olvasd be ezt a QR kódot a hitelesítő alkalmazásoddal:',

                'alt' => 'QR kód beolvasásához hitelesítő alkalmazással',

            ],

            'text_code' => [

                'instruction' => 'Vagy add meg ezt a kódot manuálisan:',

                'messages' => [
                    'copied' => 'Kimásolva',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Kérjük, mentsd el a következő helyreállítási kódokat biztonságos helyre. Csak egyszer lesznek megjelenítve, de szükséged lesz rájuk, ha elveszted a hozzáférést a hitelesítő alkalmazásodhoz:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Add meg a 6 jegyű kódot a hitelesítő alkalmazásból',

                'validation_attribute' => 'kód',

                'below_content' => 'Minden bejelentkezéskor vagy érzékeny művelet végrehajtásakor meg kell adnod a 6 jegyű kódot a hitelesítő alkalmazásodból.',

                'messages' => [

                    'invalid' => 'A megadott kód érvénytelen.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Hitelesítő alkalmazás bekapcsolása',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'A hitelesítő alkalmazás be lett kapcsolva',
        ],

    ],

];
