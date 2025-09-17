<?php

return [

    'title' => 'Registrazione',

    'heading' => 'Registrati',

    'actions' => [

        'login' => [
            'before' => 'o',
            'label' => 'accedi al tuo account',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'name' => [
            'label' => 'Nome',
        ],

        'password' => [
            'label' => 'Password',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Conferma password',
        ],

        'actions' => [

            'register' => [
                'label' => 'Registrati',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Troppi tentativi di registrazione',
            'body' => 'Riprova tra :seconds secondi.',
        ],

    ],

];
