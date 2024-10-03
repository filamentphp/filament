<?php

return [

    'title' => 'Resetează parola',

    'heading' => 'Resetează parola',

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'password' => [
            'label' => 'Parola',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Confirmă parola',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Resetează parola',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Prea multe încercări consecutive',
            'body' => 'Încearcă te rog din nou peste :seconds secunde.',
        ],

    ],

];
