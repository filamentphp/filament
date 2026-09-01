<?php

return [

    'title' => 'Пријава',

    'heading' => 'Пријава',

    'actions' => [

        'register' => [
            'before' => 'или',
            'label' => 'се региструј',
        ],

        'request_password_reset' => [
            'label' => 'Заборавили сте лозинку?',
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
                'label' => 'Пријави се',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Потврди свој идентитет',

        'subheading' => 'Како би наставили са пријавом потребно је да потврдите свој идентитет.',

        'form' => [

            'provider' => [
                'label' => 'Како желите да потврдите свој идентитет?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Потврди пријаву',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Дати акредитиви не одговарају нашим записима.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Превише покушаја пријаве',
            'body' => 'Покушајте поново за :seconds s.',
        ],

    ],

];
