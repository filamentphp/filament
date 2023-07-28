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

        'go_to_page' => [
            'label' => 'Перейти до сторінки :page',
        ],

        'next' => [
            'label' => 'Наступна',
        ],

        'previous' => [
            'label' => 'Попередня',
        ],

    ],

];
