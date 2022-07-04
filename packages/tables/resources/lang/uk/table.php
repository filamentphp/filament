<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'Пошук',
            'placeholder' => 'Пошук',
        ],

    ],

    'pagination' => [

        'label' => 'Пагінація',

        'overview' => 'Показано від :first до :last із :total результатів',

        'fields' => [

            'records_per_page' => [
                'label' => 'на сторінку',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Перейти на сторінку :page',
            ],

            'next' => [
                'label' => 'Далі',
            ],

            'previous' => [
                'label' => 'Попередній',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Фільтр',
        ],

        'open_actions' => [
            'label' => 'Відкриті дії',
        ],

        'toggle_columns' => [
            'label' => 'Переключити стовпці',
        ],

    ],

    'empty' => [
        'heading' => 'Записів не знайдено',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Скинути фільтри',
            ],

            'close' => [
                'label' => 'Закрити',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Всі',
        ],

        'select' => [
            'placeholder' => 'Всі',
        ],

    ],

    'selection_indicator' => [

        'selected_count' => 'Вибрано 1 запис.|:count записів вибрано.',

        'buttons' => [

            'select_all' => [
                'label' => 'Вибрати все :count',
            ],

            'deselect_all' => [
                'label' => 'Скасувати весь вибір',
            ],

        ],

    ],

];
