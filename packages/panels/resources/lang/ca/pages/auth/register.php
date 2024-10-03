<?php

return [

    'title' => 'Enregistrar-se',

    'heading' => 'Obriu un nou compte',

    'actions' => [

        'login' => [
            'before' => 'o',
            'label' => 'inicia la sessió amb el vostre compte',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adreça de correu electrònic',
        ],

        'name' => [
            'label' => 'Nom',
        ],

        'password' => [
            'label' => 'Contrasenya',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Confirma la contrasenya',
        ],

        'actions' => [

            'register' => [
                'label' => 'Enregistrar-se',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Massa intents de registre',
            'body' => 'Si us plau, torneu-ho a provar en :seconds segons.',
        ],

    ],

];
