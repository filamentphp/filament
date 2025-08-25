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
            'title' => 'Reset tumna a tam lutuk',
            'body' => 'Khawngaihin seconds :seconds hnuah ti nawn leh rawh.',
        ],

    ],

];
