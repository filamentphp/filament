<?php

return [

    'title' => 'Restableix la teva contrasenya',

    'heading' => 'Has oblidat la teva contrasenya?',

    'actions' => [

        'login' => [
            'label' => 'Tornar a l\'inici de sessió',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'actions' => [

            'request' => [
                'label' => 'Enviar email',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Si el teu compte no existeix, no rebràs l\'email.',
        ],

        'throttled' => [
            'title' => 'Massa sol·licituds',
            'body' => 'Si us plau, torna a provar-ho en :seconds segons.',
        ],

    ],

];
