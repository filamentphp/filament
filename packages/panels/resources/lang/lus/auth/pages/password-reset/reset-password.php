<?php

return [

    'title' => 'Reset your password',

    'heading' => 'Password tihṭhatna',

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
                'label' => 'Tihṭhatna',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Tihṭhat tumna a tam lutuk',
            'body' => 'Khawngaihin seconds :seconds hnuah ti nawn leh rawh.',
        ],

    ],

];
