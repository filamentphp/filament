<?php

return [

    'label' => 'Наигация на странициране',

    'overview' => '{1} Показване на 1 резултат|[2,*] Показване на :first до :last от общо :total резултата',

    'fields' => [

        'records_per_page' => [

            'label' => 'Резултати на страница',

            'options' => [
                'all' => 'Всички',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Първа',
        ],

        'go_to_page' => [
            'label' => 'Отиди на страница :page',
        ],

        'last' => [
            'label' => 'Последна',
        ],

        'next' => [
            'label' => 'Следваща',
        ],

        'previous' => [
            'label' => 'Предишна',
        ],

    ],

];
