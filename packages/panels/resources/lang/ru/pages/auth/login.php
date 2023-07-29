<?php

return [

    'title' => 'Авторизоваться',

    'heading' => 'Войдите в свой аккаунт',

    'form' => [

        'email' => [
            'label' => 'Адрес электронной почты',
        ],

        'password' => [
            'label' => 'Пароль',
        ],

        'remember' => [
            'label' => 'Запомнить меня',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Войти',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'Неверное имя пользователя или пароль.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Слишком много попыток входа. Пожалуйста, попробуйте еще раз через :seconds секунд.',
        ],

    ],

];
