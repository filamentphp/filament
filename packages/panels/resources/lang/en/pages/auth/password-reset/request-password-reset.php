<?php

return [

    'title' => 'Reset your password',

    'heading' => 'Forgotten your password?',

    'actions' => [

        'login' => [
            'label' => 'back to login',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email address',
        ],

        'actions' => [

            'request' => [
                'label' => 'Send email',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Too many requests',
            'body' => 'Please try again in :seconds seconds.',
        ],

    ],

];
