<?php

return [

    'title' => 'Înregistrare',

    'heading' => 'Creează cont',

    'actions' => [

        'login' => [
            'before' => 'sau',
            'label' => 'loghează-te în contul tau',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'name' => [
            'label' => 'Nume',
        ],

        'password' => [
            'label' => 'Parola',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Confirma parola',
        ],

        'actions' => [

            'register' => [
                'label' => 'Creează cont',
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
