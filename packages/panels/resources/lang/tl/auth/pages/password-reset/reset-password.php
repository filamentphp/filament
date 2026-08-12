<?php

return [

    'title' => 'I-reset ang iyong password',

    'heading' => 'I-reset ang iyong password',

    'form' => [

        'email' => [
            'label' => 'Address ng email',
        ],

        'password' => [
            'label' => 'Password',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Kumpirmahin ang password',
        ],

        'actions' => [

            'reset' => [
                'label' => 'I-reset ang password',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Masyadong maraming subok na mag-reset',
            'body' => 'Subukan ulit pagkalipas ng :seconds segundo.',
        ],

    ],

];
