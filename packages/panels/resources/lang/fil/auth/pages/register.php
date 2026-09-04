<?php

return [
    'title' => 'Mag-register',
    'heading' => 'Gumawa ng account',
    'actions' => [
        'login' => [
            'before' => 'o',
            'label' => 'mag-sign in sa account mo',
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
            'label' => 'Password mo',
            'validation_attribute' => 'password ng account',
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
            'title' => 'Masyadong maraming registration attempt',
            'body' => 'Subukan ulit pagkalipas ng :seconds segundo.',
        ],
    ],
];
