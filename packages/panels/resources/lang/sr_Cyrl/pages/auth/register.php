<?php

return [

    'title' => 'Регистрација',

    'heading' => 'Региструјте се',

    'actions' => [

        'login' => [
            'before' => 'или',
            'label' => 'Пријавите се',
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
                'label' => 'Региструјте се',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Превише покушаја регистрације',
            'body' => 'Молим вас, покушајте поново за :seconds секунди.',
        ],

    ],

];
