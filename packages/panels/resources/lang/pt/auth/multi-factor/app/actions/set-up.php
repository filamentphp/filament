<?php

return [

    'label' => 'Configurar',

    'modal' => [

        'heading' => 'Configurar aplicação de autenticação',

        'description' => <<<'BLADE'
            Precisará de uma aplicação como o Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) para completar este processo.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Digitalize este código QR com a sua aplicação de autenticação:',

                'alt' => 'Código QR para digitalizar com uma aplicação de autenticação',

            ],

            'text_code' => [

                'instruction' => 'Ou introduza este código manualmente:',

                'messages' => [
                    'copied' => 'Copiado',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Por favor, guarde os seguintes códigos de recuperação num local seguro. Serão mostrados apenas uma vez, mas precisará deles se perder o acesso à sua aplicação de autenticação:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Introduza o código de 6 dígitos da aplicação de autenticação',

                'validation_attribute' => 'código',

                'below_content' => 'Precisará de introduzir o código de 6 dígitos da sua aplicação de autenticação sempre que iniciar sessão ou realizar ações sensíveis.',

                'messages' => [

                    'invalid' => 'O código que introduziu é inválido.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Ativar aplicação de autenticação',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Aplicação de autenticação foi ativada',
        ],

    ],

];
