<?php

return [

    'title' => 'Reset your password',

    'heading' => 'Forgot password?',

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

        'sent' => [
            'body' => 'If your account doesn\'t exist, you will not receive the email.',
        ],

        'throttled' => [
            'title' => 'Too many requests',
            'body' => 'Please try again in :seconds seconds.',
        ],

    ],

];
