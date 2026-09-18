<?php

return [

    'title' => 'Бақайдгирӣ',

    'heading' => 'Эҷоди аккаунт',

    'actions' => [

        'login' => [
            'before' => 'ё',
            'label' => 'ба ҳисоби худ ворид шавед',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Суроғаи почтаи электронӣ',
        ],

        'name' => [
            'label' => 'Ном',
        ],

        'password' => [
            'label' => 'Рамз',
            'validation_attribute' => 'рамз',
        ],

        'password_confirmation' => [
            'label' => 'Тасдиқи рамз',
        ],

        'actions' => [

            'register' => [
                'label' => 'Бақайдгирӣ',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Кӯшишҳои бақайдгирӣ аз ҳад зиёд шуданд',
            'body' => 'Лутфан пас аз :seconds сония дубора кӯшиш кунед.',
        ],

    ],

];
