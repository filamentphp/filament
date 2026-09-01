<?php

return [

    'title' => 'Inici de sessió',

    'heading' => 'Accediu al vostre compte',

    'actions' => [

        'register' => [
            'before' => 'o',
            'label' => 'obrir un compte',
        ],

        'request_password_reset' => [
            'label' => 'Heu oblidat la vostra contrasenya?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'password' => [
            'label' => 'Contrasenya',
        ],

        'remember' => [
            'label' => 'Recorda\'m',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Entrar',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Verifica la teva identitat',

        'subheading' => 'Per continuar amb l\'inici de sessió, has de verificar la teva identitat.',

        'form' => [

            'provider' => [
                'label' => 'Com vols verificar-te?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Confirmar inici de sessió',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Aquestes credencials no coincideixen amb els nostres registres',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Massa intents de connexió',
            'body' => 'Si us plau, torneu-ho a provar en :seconds segons.',
        ],

    ],

];
