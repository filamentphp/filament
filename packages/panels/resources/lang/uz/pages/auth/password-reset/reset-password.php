<?php

return [

    'title' => 'Сбросить пароль',

    'heading' => 'Сбросить пароль',

    'form' => [

        'email' => [
            'label' => 'Адрес электронной почты',
        ],

        'password' => [
            'label' => 'Пароль',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Подтвердите пароль',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Сбросить пароль',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Слишком много попыток сброса',
            'body' => 'Пожалуйста, попробуйте еще раз через :seconds секунд.',
        ],

    ],

];
