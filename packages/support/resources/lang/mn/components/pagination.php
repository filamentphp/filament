<?php

return [

    'label' => 'Хуудас',

    'overview' => '{1} Нийт 1 |[2,*] Нийт :total бичлэг :first - :last',

    'fields' => [

        'records_per_page' => [

            'label' => 'хуудас бүр',

            'options' => [
                'all' => 'Бүгд',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Эхний',
        ],

        'go_to_page' => [
            'label' => 'Хуудас руу очих :page',
        ],

        'last' => [
            'label' => 'Сүүлийн',
        ],

        'next' => [
            'label' => 'Дараах',
        ],

        'previous' => [
            'label' => 'Өмнөх',
        ],

    ],

];
