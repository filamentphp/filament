<?php

return [

    'title' => 'Регистрация',

    'heading' => 'Създайте своя акаунт',

    'actions' => [

        'login' => [
            'before' => 'или',
            'label' => 'влезте в своя акаунт',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Имейл',
        ],

        'name' => [
            'label' => 'Име',
        ],

        'password' => [
            'label' => 'Парола',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Потвърдете паролата',
        ],

        'actions' => [

            'register' => [
                'label' => 'Регистрирайте се',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Твърде много опити за регистрация',
            'body' => 'Моля, опитайте отново след :seconds секунди.',
        ],

    ],

];
