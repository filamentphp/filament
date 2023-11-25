<?php

return [

    'title' => 'Regjistrohu',

    'heading' => 'Regjistrohu',

    'actions' => [

        'login' => [
            'before' => 'ose',
            'label' => 'hyni në llogarinë tuaj',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Adresa e emailit',
        ],

        'name' => [
            'label' => 'Emri',
        ],

        'password' => [
            'label' => 'Fjalëkalimi',
            'validation_attribute' => 'fjalëkalimi',
        ],

        'password_confirmation' => [
            'label' => 'Konfirmo fjalëkalimin',
        ],

        'actions' => [

            'register' => [
                'label' => 'Regjistrohu',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Too many registration attempts',
            'body' => 'Please try again in :seconds seconds.',
        ],

    ],

];
