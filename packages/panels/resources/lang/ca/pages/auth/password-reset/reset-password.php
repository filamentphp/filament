<?php

return [

    'title' => 'Restableix la teva contrasenya',

    'heading' => 'Restableix la teva contrasenya',

    'form' => [

        'email' => [
            'label' => 'Adreça de correu electrònic',
        ],

        'password' => [
            'label' => 'Contrasenya',
            'validation_attribute' => 'contrasenya',
        ],

        'password_confirmation' => [
            'label' => 'Confirma la contrasenya',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Restableix la contrasenya',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Massa intents de restabliment',
            'body' => 'Si us plau, torna-ho a provar en :seconds segons.',
        ],

    ],

];
