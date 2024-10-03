<?php

return [

    'title' => 'Регистрация',

    'heading' => 'Регистрация учетной записи',

    'actions' => [

        'login' => [
            'before' => 'или',
            'label' => 'войти в свой аккаунт',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Адрес электронной почты',
        ],

        'name' => [
            'label' => 'Имя',
        ],

        'password' => [
            'label' => 'Пароль',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'Подтвердите пароль',
        ],

        'actions' => [

            'register' => [
                'label' => 'Зарегистрироваться',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Слишком много попыток регистрации',
            'body' => 'Пожалуйста, попробуйте еще раз через :seconds секунд.',
        ],

    ],

];
