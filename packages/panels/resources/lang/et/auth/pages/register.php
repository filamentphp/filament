<?php

return [

    'title' => 'Registreeru',

    'heading' => 'Registreeru',

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
            'label' => 'Parool',
            'validation_attribute' => 'parool',
        ],

        'password_confirmation' => [
            'label' => 'Kinnita parool',
        ],

        'actions' => [

            'register' => [
                'label' => 'Loo uus konto',
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
