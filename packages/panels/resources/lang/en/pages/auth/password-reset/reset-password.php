<?php

return [

    'title' => 'Reset your password',

    'heading' => 'Reset your password',

    'form' => [

        'email' => [
            'label' => 'Email address',
        ],

        'password' => [
            'label' => 'Password',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Confirm password',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Reset password',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Too many reset attempts',
            'body' => 'Please try again in :seconds seconds.',
        ],

    ],

];
