<?php

return [

    'label' => 'Aktiver',

    'modal' => [

        'heading' => 'Aktiver autentiseringsapp for 2FA',

        'description' => <<<'BLADE'
            Du trenger en app som Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) for å fullføre denne prosessen.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Skann denne QR‑koden med autentiseringsappen din:',

                'alt' => 'QR‑kode som kan skannes med en autentiseringsapp',

            ],

            'text_code' => [

                'instruction' => 'Eller skriv inn denne koden manuelt:',

                'messages' => [
                    'copied' => 'Kopiert',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Lagre disse gjenopprettingskodene på et trygt sted. De vises bare én gang, men trengs hvis du mister tilgang til autentiseringsappen.',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Skriv inn 6‑sifret kode fra autentiseringsappen',

                'validation_attribute' => 'kode',

                'below_content' => 'Du må skrive inn en 6‑sifret kode fra autentiseringsappen hver gang du logger inn eller utfører sensitive handlinger.',

                'messages' => [

                    'invalid' => 'Koden du skrev inn er ugyldig.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Aktiver autentiseringsappen',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Autentiseringsappen er aktivert',
        ],

    ],

];
