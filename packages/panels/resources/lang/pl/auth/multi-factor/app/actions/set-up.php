<?php

return [

    'label' => 'Skonfiguruj',

    'modal' => [

        'heading' => 'Skonfiguruj aplikację uwierzytelniającą',

        'description' => <<<'BLADE'
            Zainstaluj aplikację Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>), aby zakończyć ten proces.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Zeskanuj kod QR za pomocą aplikacji uwierzytelniającej:',

                'alt' => 'Kod QR do zeskanowania za pomocą aplikacji uwierzytelniającej',

            ],

            'text_code' => [

                'instruction' => 'Lub wpisz kod ręcznie:',

                'messages' => [
                    'copied' => 'Skopiowano',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Zapisz poniższe kody odzyskiwania w bezpiecznym miejscu. Zostaną one wyświetlone tylko raz, ale będą potrzebne, jeśli stracisz dostęp do aplikacji uwierzytelniającej:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Wprowadź 6-cyfrowy kod z aplikacji uwierzytelniającej',

                'validation_attribute' => 'kod',

                'below_content' => 'Kod z aplikacji uwierzytelniającej będzie wymagany podczas logowania lub wykonywania wrażliwych czynności.',

                'messages' => [

                    'invalid' => 'Wprowadzony kod jest nieprawidłowy.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Włącz aplikację uwierzytelniającą',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Aplikacja uwierzytelniająca została włączona',
        ],

    ],

];
