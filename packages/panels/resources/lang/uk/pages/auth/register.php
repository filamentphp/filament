<?php

return [

    'title' => 'Зареєструйтеся',

    'heading' => 'Зареєструйтеся',

    'actions' => [

        'login' => [
            'before' => 'або',
            'label' => 'ввійдіть у свій акаунт',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Електронна пошта',
        ],

        'name' => [
            'label' => 'Ім\'я',
        ],

        'password' => [
            'label' => 'Пароль',
            'validation_attribute' => 'пароль',
        ],

        'password_confirmation' => [
            'label' => 'Введіть пароль ще раз',
        ],

        'actions' => [

            'register' => [
                'label' => 'Зареєструватися',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Забагато спроб зареєструватися',
            'body' => 'Будь ласка, спробуйте ще раз через :seconds секунд.',
        ],

    ],

];
