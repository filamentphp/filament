<?php

return [

    'title' => 'Възстановяване на парола',

    'heading' => 'Възстановяване на парола',

    'form' => [

        'email' => [
            'label' => 'Имейл',
        ],

        'password' => [
            'label' => 'Парола',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Потвърдете паролата',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Възстановете паролата си',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Твърде много опити за възстановяване на парола',
            'body' => 'Моля, опитайте отново след :seconds секунди.',
        ],

    ],

];
