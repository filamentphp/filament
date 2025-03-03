<?php

return [

    'title' => 'Ресетуј своју лозинку',

    'heading' => 'Ресетуј своју лозинку',

    'form' => [

        'email' => [
            'label' => 'Адреса имејла',
        ],

        'password' => [
            'label' => 'Лозинка',
            'validation_attribute' => 'лозинка',
        ],

        'password_confirmation' => [
            'label' => 'Потврди лозинку',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Ресетуј лозинку',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Превише покушаја ресетовања.',
            'body' => 'Молимо вас, покушајте поново за :seconds секунди.',
        ],

    ],

];
