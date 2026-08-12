<?php

return [

    'title' => 'Mag-register',

    'heading' => 'Gumawa ng account',

    'actions' => [

        'login' => [
            'before' => 'o',
            'label' => 'mag-sign in sa iyong account',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Address ng email',
        ],

        'name' => [
            'label' => 'Pangalan',
        ],

        'password' => [
            'label' => 'Password',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Kumpirmahin ang password',
        ],

        'actions' => [

            'register' => [
                'label' => 'Gumawa ng account',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Masyadong maraming subok sa registration',
            'body' => 'Subukan ulit pagkalipas ng :seconds segundo.',
        ],

    ],

];
