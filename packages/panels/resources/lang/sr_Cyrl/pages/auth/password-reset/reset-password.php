<?php

return [

    'title' => 'Ресетуј своју лозинку',

    'heading' => 'Ресетуј своју лозинку',

    'form' => [

        'email' => [
            'label' => 'Адреса е-поште',
        ],

        'password' => [
            'label' => 'Лозинка',
            'validation_attribute' => 'лозинка',
        ],

        'password_confirmation' => [
            'label' => 'Потврдите лозинку',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Ресетуј лозинку',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Предао се превише покушаја ресетовања.',
            'body' => 'Молим вас, покушајте поново за :seconds секунди.',
        ],

    ],

];
