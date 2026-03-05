<?php

return [

    'label' => 'Įgalinti',

    'modal' => [

        'heading' => 'Įgalinti autentifikavimo programą',

        'description' => <<<'BLADE'
            Jums reikės programos tokios kaip Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) norint užbaigti šį procesą.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Nuskaitykite šį QR kodą naudodami autentifikavimo programą:',

                'alt' => 'QR kodas, kurį reikia nuskaityti naudojant autentifikavimo programą',

            ],

            'text_code' => [

                'instruction' => 'Arba įveskite šį kodą rankiniu būdu:',

                'messages' => [
                    'copied' => 'Nukopijuota',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Prašome išsaugoti šiuos atsarginius kodus saugioje vietoje. Jie bus rodomi tik vieną kartą, bet jums reikės jų, jei prarasite prieigą prie autentifikavimo programos:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Įveskite 6 skaitmenų kodą iš autentifikavimo programos',

                'validation_attribute' => 'code',

                'below_content' => 'Jums reikės įvesti 6 skaitmenų kodą iš savo autentifikavimo programos kaskart, kai prisijungsite arba atliksite jautrius veiksmus.',

                'messages' => [

                    'invalid' => 'Įvestas kodas yra neteisingas.',

                    'rate_limited' => 'Per daug bandymų. Pabandykite vėliau.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Įgalinti autentifikavimo programą',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Autentifikavimo programa įjungta',
        ],

    ],

];
