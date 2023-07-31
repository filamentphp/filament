<?php

return [

    'label' => 'Пагинация',

    'overview' => 'Показано с :first по :last из :total',

    'fields' => [

        'records_per_page' => [

            'label' => 'на страницу',

            'options' => [
                'all' => 'Все',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Перейти к странице :page',
        ],

        'next' => [
            'label' => 'Следующая',
        ],

        'previous' => [
            'label' => 'Предыдущая',
        ],

    ],

];
