<?php

return [

    'title' => 'Login',

    'heading' => 'Luhna',

    'actions' => [

        'register' => [
            'before' => 'emaw',
            'label' => 'account nei turin in ziak lut rawh',
        ],

        'request_password_reset' => [
            'label' => 'Password i theihnghilh em?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'password' => [
            'label' => 'Password',
        ],

        'remember' => [
            'label' => 'Inhriatrengna',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Luhna',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'I nihna finfiah rawh',

        'subheading' => 'Lût chhunzawm turin, i nihna finfiah phawt a ngai.',

        'form' => [

            'provider' => [
                'label' => 'Engtiang a finfiah nge i duh?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Luhna nemnghehna',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Hemi credentials hi kan records neihah a awmlo.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Luh tumna a tam lutuk',
            'body' => 'Khawngaihin seconds :seconds hnuah ti nawn leh rawh.',
        ],

    ],

];
