<?php

return [

    'title' => 'Login',

    'heading' => 'Sign in',

    'actions' => [

        'register' => [
            'before' => 'or',
            'label' => 'account nei turin sign up rawh',
        ],

        'request_password_reset' => [
            'label' => 'Password i theihnghilh em?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email address',
        ],

        'password' => [
            'label' => 'Password',
        ],

        'remember' => [
            'label' => 'Remember me',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Sign in',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'I nihna verify rawh',

        'subheading' => 'Signing in chhunzawm turin, i nihna verify phawt a ngai.',

        'form' => [

            'provider' => [
                'label' => 'Engtiang a verify nge i duh?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Confirm sign in',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Hemi credentials hi kan records neihah a awmlo.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Login tumna a tam lutuk',
            'body' => 'Khawngaihin seconds :seconds hnuah ti nawn leh rawh.',
        ],

    ],

];
