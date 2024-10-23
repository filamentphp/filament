<?php

return [

    'title' => 'რეგისტრაცია',

    'heading' => 'რეგისტრაცია',

    'actions' => [

        'login' => [
            'before' => 'ან',
            'label' => 'ავტორიზაცია თქვენს ანგარიშზე',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ელფოსტის მისამართი',
        ],

        'name' => [
            'label' => 'სახელი',
        ],

        'password' => [
            'label' => 'პაროლი',
            'validation_attribute' => 'პაროლი',
        ],

        'password_confirmation' => [
            'label' => 'დაადასტურეთ პაროლი',
        ],

        'actions' => [

            'register' => [
                'label' => 'დარეგისტრირება',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'ზედმეტად ბევრი რეგისტრაციის მცდელობა',
            'body' => 'გთხოვთ, სცადეთ ხელახლა :seconds წამში.',
        ],

    ],

];
