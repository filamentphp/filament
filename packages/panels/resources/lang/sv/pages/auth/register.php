<?php

return [

    'title' => 'Registrera',

    'heading' => 'Skapa konto',

    'actions' => [

        'login' => [
            'before' => 'eller',
            'label' => 'logga in på ditt konto',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Mejladress',
        ],

        'name' => [
            'label' => 'Namn',
        ],

        'password' => [
            'label' => 'Lösenord',
            'validation_attribute' => 'lösenord',
        ],

        'password_confirmation' => [
            'label' => 'Bekräfta lösenord',
        ],

        'actions' => [

            'register' => [
                'label' => 'Skapa konto',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'För många registreringsförsök',
            'body' => 'Vänligen försök igen om :seconds sekunder.',
        ],

    ],

];
