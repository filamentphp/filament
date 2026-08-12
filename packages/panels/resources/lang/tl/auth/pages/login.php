<?php

return [

    'title' => 'Pag-login',

    'heading' => 'Mag-sign in',

    'actions' => [

        'register' => [
            'before' => 'o',
            'label' => 'gumawa ng account',
        ],

        'request_password_reset' => [
            'label' => 'Nakalimutan ang password?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Address ng email',
        ],

        'password' => [
            'label' => 'Password',
        ],

        'remember' => [
            'label' => 'Tandaan ako',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Mag-sign in',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'I-verify ang iyong identity',

        'subheading' => 'Para magpatuloy sa pag-sign in, kailangan mong i-verify ang iyong identity.',

        'form' => [

            'provider' => [
                'label' => 'Paano mo gustong mag-verify?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Kumpirmahin ang pag-sign in',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Hindi tumutugma ang credentials na ito sa aming records.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Masyadong maraming subok sa login',
            'body' => 'Subukan ulit pagkalipas ng :seconds segundo.',
        ],

    ],

];
