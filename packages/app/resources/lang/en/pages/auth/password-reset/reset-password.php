<?php

return [

    'title' => 'Reset your password',

    'heading' => 'Reset your password',

    'buttons' => [

        'reset' => [
            'label' => 'Reset password',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'Email address',
        ],

        'password' => [
            'label' => 'Password',
            'validation_attribute' => 'password',
        ],

        'passwordConfirmation' => [
            'label' => 'Confirm your password',
        ],

    ],

    'messages' => [
        'throttled' => 'Too many reset attempts. Please try again in :seconds seconds.',
    ],

];
