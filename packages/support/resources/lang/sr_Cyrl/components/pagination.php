<?php

return [

    'label' => 'Навигација страницама',

    'overview' => '{1} Приказан је 1 резултат|[2,*] Приказано је :first до :last од укупно :total резултата',

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
            'label' => 'Последња',
        ],

        'next' => [
            'label' => 'Напред',
        ],

        'previous' => [
            'label' => 'Назад',
        ],

    ],

];
