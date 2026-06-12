<?php

return [

    'label' => 'Configurar',

    'modal' => [

        'heading' => 'Configurar l\'aplicació d\'autenticació',

        'description' => <<<'BLADE'
            Necessitaràs una aplicació com Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) per completar aquest procés.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Escanegi aquest codi QR amb la seva aplicació d\'autenticació:',

                'alt' => 'Codi QR per escanejar amb una aplicació d\'autenticació',

            ],

            'text_code' => [

                'instruction' => 'O introdueix aquest codi manualment:',

                'messages' => [
                    'copied' => 'Copiat',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Guarda els següents codis de recuperació en un lloc segur. Només es mostraran una vegada, i els necessitaràs si perds l\'accés a la teva aplicació d\'autenticació:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Introduex el codi de 6 dígits de l\'aplicació d\'autenticació',

                'validation_attribute' => 'codi',

                'below_content' => 'Necesitaràs introduïr el codi de 6 dígito de la teva aplicació d\'autenticació cada vegada que inciis sessió o realitzis accions sensibles.',

                'messages' => [

                    'invalid' => 'El codi introduït no és vàlid.',

                    'rate_limited' => 'Massa intents. Si us plau, intenta-ho més tard.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Habilitar aplicació d\'autenticació',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'L\'aplicació d\'autenticació ha sigut activada',
        ],

    ],

];
