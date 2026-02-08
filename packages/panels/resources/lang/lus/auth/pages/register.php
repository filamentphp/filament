<?php

return [

    'title' => 'Register',

    'heading' => 'In ziah luhna',

    'actions' => [

        'login' => [
            'before' => 'emaw',
            'label' => 'I account ah lut rawh',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'name' => [
            'label' => 'Hming',
        ],

        'password' => [
            'label' => 'Password',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Password nemnghehna',
        ],

        'actions' => [

            'register' => [
                'label' => 'Ziah luhna',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'In ziah luh tumna a tam lutuk',
            'body' => 'Khawngaihin seconds :seconds hnuah ti nawn leh rawh.',
        ],

    ],

];
