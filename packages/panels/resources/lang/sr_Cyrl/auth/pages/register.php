<?php

return [

    'title' => 'Регистрација',

    'heading' => 'Регистрација',

    'actions' => [

        'login' => [
            'before' => 'или',
            'label' => 'се пријавите на постојећи налог',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Адреса е-поште',
        ],

        'name' => [
            'label' => 'Име',
        ],

        'password' => [
            'label' => 'Лозинка',
            'validation_attribute' => 'лозинка',
        ],

        'password_confirmation' => [
            'label' => 'Потврдите лозинку',
        ],

        'actions' => [

            'register' => [
                'label' => 'Региструј се',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Превише покушаја регистрације',
            'body' => 'Покушајте поново за :seconds s.',
        ],

    ],

];
