<?php

return [

    'label' => 'Configurar',

    'modal' => [

        'heading' => 'Configurar la aplicación de autenticación',

        'description' => <<<'BLADE'
            Usted necesitará una aplicación como Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) para completar este proceso.
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'Escanée este código QR con su aplicación de autenticación:',

                'alt' => 'Código QR para escanear con una aplicación de autenticación',

            ],

            'text_code' => [

                'instruction' => 'O ingrese este código manualmente:',

                'messages' => [
                    'copied' => 'Copiado',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'Guarde los siguientes códigos de recuperación en un lugar seguro. Solo se mostrarán una vez, y los necesitará si pierde el acceso a su aplicación de autenticación:',

            ],

        ],

        'form' => [

            'code' => [

                'label' => 'Ingrese el código de 6 dígitos de la aplicación de autenticación',

                'validation_attribute' => 'código',

                'below_content' => 'Necesitará ingresar el código de 6 dígitos de su aplicación de autenticación cada vez que inicie sesión o realice acciones sensibles.',

                'messages' => [

                    'invalid' => 'El código ingresado no es válido.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Habilitar aplicación de autenticación',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'La aplicación de autenticación ha sido habilitada',
        ],

    ],

];
