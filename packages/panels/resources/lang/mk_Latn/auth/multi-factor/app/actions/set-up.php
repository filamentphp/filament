<?php

return [

    'label' => 'Postavi',

    'modal' => [

        'heading' => 'Postavi aplikаcija za avtentifikacija',

        'description' => <<<'BLADE'
            Ќe vi treba aplikаcija kako Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) za da go završite ovoj proces.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Skenirаj go ovoj QR kod so vašata aplikаcija za avtentifikacija:',

                'alt' => 'QR kod za skeniranje so aplikаcija za avtentifikacija',

            ],

            'text_code' => [

                'instruction' => 'Ili vnesete go ovoj kod rаčno:',

                'messages' => [
                    'copied' => 'Kopirano',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Ve molime začuvајte gi slednite kodovi za obnovуvanje na bezbedno mesto. Tie ќe bidat prikažаni samo ednаš, no ќe vi trebаat ako izgubite pristаp do vašata aplikаcija za avtentifikacija:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Vnesete go 6-cifreniot kod od aplikаcijata za avtentifikacija',

                'validation_attribute' => 'kod',

                'below_content' => 'Ќe treba da go vnesete 6-cifreniot kod od vašata aplikаcija za avtentifikacija sekoj pat koga ќe se najavite ili izvršite čuvstvitelni akcii.',

                'messages' => [

                    'invalid' => 'Kodot što go vnesovte ne e validen.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Ovozmoži aplikаcija za avtentifikacija',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Aplikаcijata za avtentifikacija e ovozmožena',
        ],

    ],

];
