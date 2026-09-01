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
            'label' => 'Password mo',
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
        'heading' => 'I-verify ang pagkakakilanlan mo',
        'subheading' => 'Para magpatuloy sa pag-sign in, kailangan mong i-verify ang pagkakakilanlan mo.',
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
        'failed' => 'Hindi tugma ang mga credential na ito sa aming mga rekord.',
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'Masyadong maraming login attempt',
            'body' => 'Subukan ulit pagkalipas ng :seconds segundo.',
        ],
    ],
];
