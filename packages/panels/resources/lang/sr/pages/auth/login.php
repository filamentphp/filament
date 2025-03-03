<?php

return [

    'title' => 'Пријава',

    'heading' => 'Пријави се',

    'actions' => [

        'register' => [
            'before' => 'или',
            'label' => 'региструј налог',
        ],

        'request_password_reset' => [
            'label' => 'Заборављена лозинка?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Имејл адреса',
        ],

        'password' => [
            'label' => 'Лозинка',
        ],

        'remember' => [
            'label' => 'Запамти ме',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Пријави се',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Погрешно корисничко име или лозинка',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Превише покушаја пријаве.',
            'body' => 'Молим те, покушај поново за :seconds секунди.',
        ],

    ],

];
