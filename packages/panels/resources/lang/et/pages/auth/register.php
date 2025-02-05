<?php

return [

    'title' => 'Registreerimine',

    'heading' => 'Loo konto',

    'actions' => [

        'login' => [
            'before' => 'või',
            'label' => 'logi sisse olemasoleva kontoga',
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
            'label' => 'Salasõna',
            'validation_attribute' => 'salasõna',
        ],

        'password_confirmation' => [
            'label' => 'Kinnita salasõna',
        ],

        'actions' => [

            'register' => [
                'label' => 'Loo konto',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Liiga palju registreerimise katseid',
            'body' => 'Palun proovi uuesti :seconds sekundi pärast.',
        ],

    ],

];
