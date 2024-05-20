<?php

return [

    'title' => 'Вход',

    'heading' => 'Вход в профила си',

    'actions' => [

        'register' => [
            'before' => 'или',
            'label' => 'създайте нов акаунт',
        ],

        'request_password_reset' => [
            'label' => 'Забравена парола?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Имейл',
        ],

        'password' => [
            'label' => 'Парола',
        ],

        'remember' => [
            'label' => 'Запомни ме',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Влезте в профила си',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Грешен имейл или парола.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Твърде много опити за вход',
            'body' => 'Моля, опитайте отново след :seconds секунди.',
        ],

    ],

];
