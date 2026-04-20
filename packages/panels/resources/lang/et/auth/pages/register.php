<?php

return [

    'title' => 'Registreerimine',

    'heading' => 'Registreeruge',

    'actions' => [

        'login' => [
            'before' => 'või',
            'label' => 'logige sisse oma kontole',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-posti aadress',
        ],

        'name' => [
            'label' => 'Nimi',
        ],

        'password' => [
            'label' => 'Parool',
            'validation_attribute' => 'parool',
        ],

        'password_confirmation' => [
            'label' => 'Kinnita parool',
        ],

        'actions' => [

            'register' => [
                'label' => 'Registreeruge',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Liiga palju registreerimiskatseid',
            'body' => 'Palun proovige uuesti :seconds sekundi pärast.',
        ],

    ],

];
