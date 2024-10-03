<?php

return [

    'title' => 'Autentificare',

    'heading' => 'Loghează-te în contul tau',

    'actions' => [

        'register' => [
            'before' => 'sau',
            'label' => 'creează cont',
        ],

        'request_password_reset' => [
            'label' => 'Ai uitat parola?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'password' => [
            'label' => 'Parola',
        ],

        'remember' => [
            'label' => 'Ține-mă minte',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Autentificare',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Emailul sau parola nu sunt corecte',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Te rugăm să aștepți :seconds secunde înainte de a încerca din nou',
            'body' => 'Te rugăm sa reîncerci in :seconds secunde.',
        ],

    ],

];
