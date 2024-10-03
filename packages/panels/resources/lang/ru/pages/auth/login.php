<?php

return [

    'title' => 'Авторизоваться',

    'heading' => 'Войдите в свой аккаунт',

    'actions' => [

        'register' => [
            'before' => 'или',
            'label' => 'зарегистрируйте учетную запись',
        ],

        'request_password_reset' => [
            'label' => 'Забыли свой пароль?',
        ],

    ],

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
            'title' => 'Слишком много попыток входа',
            'body' => 'Пожалуйста, попробуйте еще раз через :seconds секунд.',
        ],

    ],

];
