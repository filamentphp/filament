<?php

return [

    'title' => 'Регистрација',

    'heading' => 'Региструј се',

    'actions' => [

        'login' => [
            'before' => 'или',
            'label' => 'се пријави',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Имејл адреса',
        ],

        'name' => [
            'label' => 'Име',
        ],

        'password' => [
            'label' => 'Лозинка',
            'validation_attribute' => 'лозинка',
        ],

        'password_confirmation' => [
            'label' => 'Потврди лозинку',
        ],

        'actions' => [

            'register' => [
                'label' => 'Региструј се',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Превише покушаја регистрације.',
            'body' => 'Покушај поново za :seconds секунди.',
        ],

    ],

];
