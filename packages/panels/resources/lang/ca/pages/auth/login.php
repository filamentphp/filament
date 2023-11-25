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
            'label' => 'Adreça de correu electrònic',
        ],

        'password' => [
            'label' => 'Contrasenya',
        ],

        'remember' => [
            'label' => 'Recorda\'m',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Accedir',
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
