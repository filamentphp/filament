<?php

return [
    'title' => 'I-reset ang password mo',
    'heading' => 'I-reset ang password mo',
    'form' => [
        'email' => [
            'label' => 'Address ng email',
        ],
        'password' => [
            'label' => 'Password mo',
            'validation_attribute' => 'password ng account',
        ],
        'password_confirmation' => [
            'label' => 'Kumpirmahin ang password',
        ],
        'actions' => [
            'reset' => [
                'label' => 'I-reset ang password',
            ],
        ],
    ],
    'notifications' => [
        'throttled' => [
            'title' => 'Masyadong maraming reset attempt',
            'body' => 'Subukan ulit pagkalipas ng :seconds segundo.',
        ],
    ],
];
