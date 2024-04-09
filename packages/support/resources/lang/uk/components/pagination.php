<?php

return [

    'label' => 'Пагінація',

    'overview' => 'Показано з :first по :last з :total',

    'fields' => [

        'records_per_page' => [

            'label' => 'на сторінку',

            'options' => [
                'all' => 'Всі',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Перша',
        ],

        'go_to_page' => [
            'label' => 'Перейти до сторінки :page',
        ],

        'last' => [
            'label' => 'Остання',
        ],

        'next' => [
            'label' => 'Наступна',
        ],

        'previous' => [
            'label' => 'Попередня',
        ],

    ],

];
