<?php

return [

    'label' => 'Configurar',

    'modal' => [

        'heading' => 'Configurar app autenticador',

        'description' => <<<'BLADE'
            Você precisará de um aplicativo como o Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) para concluir este processo.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Escaneie este QR Code com seu app autenticador:',

                'alt' => 'QR Code para escanear com um app autenticador',

            ],

            'text_code' => [

                'instruction' => 'Ou insira este código manualmente:',

                'messages' => [
                    'copied' => 'Copiado',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Salve os seguintes códigos de recuperação em um local seguro. Eles serão exibidos apenas uma vez, mas você precisará deles se perder o acesso ao seu app autenticador:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Digite o código de 6 dígitos do app autenticador',

                'validation_attribute' => 'código',

                'below_content' => 'Você precisará inserir o código de 6 dígitos do seu app autenticador sempre que fizer login ou realizar ações sensíveis.',

                'messages' => [

                    'invalid' => 'O código informado é inválido.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Ativar app autenticador',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'App autenticador ativado',
        ],

    ],

];
