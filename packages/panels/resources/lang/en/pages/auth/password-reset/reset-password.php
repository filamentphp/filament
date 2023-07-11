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

    'messages' => [
        'throttled' => 'Too many reset attempts. Please try again in :seconds seconds.',
    ],

];
