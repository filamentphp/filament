<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Поиск',
            'placeholder' => 'Поиск',
        ],

    ],

    'pagination' => [

        'label' => 'навигация разбивки на страницы',

        'overview' => 'показ :first до :last из :total результаты',

        'fields' => [

            'records_per_page' => [
                'label' => 'на страницу',
            ],

        ],

        'buttons' => [

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

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Фильтр',
        ],

        'open_actions' => [
            'label' => 'Open actions',
        ],

        'toggle_columns' => [
            'label' => 'Переключить столбцы',
        ],

    ],

    'empty' => [
        'heading' => 'не найдено записей',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Сбросить фильтры',
            ],

            'close' => [
                'label' => 'Закрыть',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Все',
        ],

        'select' => [
            'placeholder' => 'Все',
        ],

    ],

    'selection_indicator' => [

        'selected_count' => 'Выбрана 1 запись.|Выбрано :count записей.',

        'buttons' => [

            'select_all' => [
                'label' => 'Выбрать все :count',
            ],

            'deselect_all' => [
                'label' => 'Убрать выделение со всех',
            ],

        ],

    ],

];
