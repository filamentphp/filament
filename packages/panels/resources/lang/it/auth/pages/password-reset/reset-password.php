<?php

return [

    'title' => 'Reimposta la tua password',

    'heading' => 'Reimposta la tua password',

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'password' => [
            'label' => 'Password',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Conferma password',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Reimposta password',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Troppi tentativi di reimpostazione',
            'body' => 'Riprova tra :seconds secondi.',
        ],

    ],

];
