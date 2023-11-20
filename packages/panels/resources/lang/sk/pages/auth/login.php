<?php

return [

    'title' => 'Prihlásenie',

    'heading' => 'Prihláste sa',

    'actions' => [

        'register' => [
            'before' => 'or',
            'label' => 'sign up for an account',
        ],

        'request_password_reset' => [
            'label' => 'Zabudnuté heslo?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Emailová adresa',
        ],

        'password' => [
            'label' => 'Heslo',
        ],

        'remember' => [
            'label' => 'Zapamätať si prihlásenie',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Prihlásiť sa',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Zadané údaje sú nesprávne.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Príliš veľa pokusov o prihlásenie',
            'body' => 'Prosím počkajte :seconds sekúnd.',
        ],

    ],

];
