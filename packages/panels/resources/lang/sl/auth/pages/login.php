<?php

return [

    'title' => 'Prijava',

    'heading' => 'Prijava',

    'actions' => [

        'register' => [
            'before' => 'ali',
            'label' => 'ustvarite račun',
        ],

        'request_password_reset' => [
            'label' => 'Ste pozabili geslo?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'E-poštni naslov',
        ],

        'password' => [
            'label' => 'Geslo',
        ],

        'remember' => [
            'label' => 'Zapomni si me',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Prijava',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Ti podatki se ne ujemajo z našimi.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Preveč poskusov prijave',
            'body' => 'Poskusite znova čez :seconds sekund.',
        ],

    ],

];
