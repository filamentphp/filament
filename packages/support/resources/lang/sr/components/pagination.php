<?php

return [

    'label' => 'Навигација страница',

    'overview' => '{1} Приказан 1 резултат|[2,*] Приказани :first до :last од укупно :total резултата',

    'fields' => [

        'records_per_page' => [

            'label' => 'По страници',

            'options' => [
                'all' => 'Све',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Прва',
        ],

        'go_to_page' => [
            'label' => 'Иди на страницу :page',
        ],

        'last' => [
            'label' => 'Задња',
        ],

        'next' => [
            'label' => 'Напред',
        ],

        'previous' => [
            'label' => 'Назад',
        ],

    ],

];
