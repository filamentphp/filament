<?php

return [

    'title' => 'Пријава',

    'heading' => 'Пријавите се',

    'actions' => [

        'register' => [
            'before' => 'или',
            'label' => 'се регистрирајте за кориснички налог',
        ],

        'request_password_reset' => [
            'label' => 'Заборављена лозинка?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Адреса е-поште',
        ],

        'password' => [
            'label' => 'Лозинка',
        ],

        'remember' => [
            'label' => 'Запамти ме',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Пријавите се',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Погрешно корисничко име или лозинка',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Превише покушаја пријаве',
            'body' => 'Молим вас, покушајте поново за :seconds секунди.',
        ],

    ],

];
