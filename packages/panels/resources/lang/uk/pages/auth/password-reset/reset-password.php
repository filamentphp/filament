<?php

return [

    'title' => 'Скинути пароль',

    'heading' => 'Скинути пароль',

    'form' => [

        'email' => [
            'label' => 'Електронна пошта',
        ],

        'password' => [
            'label' => 'Пароль',
            'validation_attribute' => 'пароль',
        ],

        'password_confirmation' => [
            'label' => 'Введіть пароль ще раз',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Скинути пароль',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Забагато спроб скинути пароль',
            'body' => 'Будь ласка, спробуйте ще раз через :seconds секунд.',
        ],

    ],

];
