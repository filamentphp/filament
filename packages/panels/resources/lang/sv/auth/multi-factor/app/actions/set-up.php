<?php

return [

    'label' => 'Konfigurera',

    'modal' => [

        'heading' => 'Konfigurera autentiseringsapp',

        'description' => <<<'BLADE'
            Du behöver en app som Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) för att slutföra den här processen.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Skanna denna QR-kod med din autentiseringsapp:',

                'alt' => 'QR-kod att skanna med en autentiseringsapp',

            ],

            'text_code' => [

                'instruction' => 'Eller ange denna kod manuellt:',

                'messages' => [
                    'copied' => 'Kopierat',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Spara följande återställningskoder på en säker plats. De visas endast en gång, men du behöver dem om du förlorar tillgången till din autentiseringsapp:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Ange den 6-siffriga koden från autentiseringsappen',

                'validation_attribute' => 'kod',

                'below_content' => 'Du behöver ange den 6-siffriga koden från din autentiseringsapp varje gång du loggar in eller utför känsliga åtgärder.',

                'messages' => [

                    'invalid' => 'Koden du angav är ogiltig.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Aktivera autentiseringsapp',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Autentiseringsapp har aktiverats',
        ],

    ],

];
