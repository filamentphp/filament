<?php

return [

    'label' => 'Configurazione',

    'modal' => [

        'heading' => 'Configura l\'app di autenticazione',

        'description' => <<<'BLADE'
            Avrai bisogno di un'app come Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) per completare questo processo.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Scansiona questo codice QR con la tua app di autenticazione:',

                'alt' => 'Codice QR per l\'app di autenticazione',

            ],

            'text_code' => [

                'instruction' => 'Oppure inserisci questo codice manualmente:',

                'messages' => [
                    'copied' => 'Copiato',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Si prega di salvare i seguenti codici di recupero in un luogo sicuro. Saranno mostrati solo una volta, ma ne avrai bisogno se perdi l\'accesso alla tua app di autenticazione:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Inserisci il codice di 6 cifre dall\'app di autenticazione',

                'validation_attribute' => 'code',

                'below_content' => 'Dovrai inserire il codice di 6 cifre dalla tua app di autenticazione ogni volta che accedi o esegui azioni sensibili.',

                'messages' => [

                    'invalid' => 'Il codice inserito non è valido.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Abilita l\'app di autenticazione',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'L\'app di autenticazione è stata abilitata',
        ],

    ],

];
