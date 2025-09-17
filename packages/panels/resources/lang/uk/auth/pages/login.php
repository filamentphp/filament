<?php

return [

    'title' => 'Логін',

    'heading' => 'Увійдіть у свій акаунт',

    'actions' => [

        'register' => [
            'before' => 'або',
            'label' => 'зареєструйтеся',
        ],

        'request_password_reset' => [
            'label' => 'Забули пароль?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Електронна пошта',
        ],

        'password' => [
            'label' => 'Пароль',
        ],

        'remember' => [
            'label' => 'Запам’ятай мене',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'Увійти',
            ],

        ],

    ],

    'multi_factor' => [

        'heading' => 'Підтвердіть свою особу',

        'subheading' => 'Щоб продовжити вхід, потрібно підтвердити свою особу.',

        'form' => [

            'provider' => [
                'label' => 'Як ви бажаєте підтвердити вхід?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'Підтвердити вхід',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'Ці дані не відповідають нашим записам.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Забагато спроб входу в систему',
            'body' => 'Будь ласка, спробуйте ще раз через :seconds секунд.',
        ],

    ],

];
