<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'і :count ще',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Пошук',
            'placeholder' => 'Пошук',
        ],

    ],

    'pagination' => [

        'label' => 'Пагінація',

        'overview' => 'Показано з :first по :last з :total',

        'fields' => [

            'records_per_page' => [
                'label' => 'на сторінку',
            ],

        ],

        'buttons' => [

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

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Зберегти порядок',
        ],

        'enable_reordering' => [
            'label' => 'Змінити порядок',
        ],

        'filter' => [
            'label' => 'Фільтр',
        ],

        'open_actions' => [
            'label' => 'Відкрити дії',
        ],

        'toggle_columns' => [
            'label' => 'Переключити стовпці',
        ],

    ],

    'empty' => [
        'heading' => 'Не знайдено записів',
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

        'trashed' => [

            'label' => 'Видалені записи',

            'only_trashed' => 'Тільки видалені записи',

            'with_trashed' => 'З видаленими записами',

            'without_trashed' => 'Без видалених записів',

        ],

    ],

    'reorder_indicator' => 'Drag-n-drop порядок записів.',

    'selection_indicator' => [

        'selected_count' => 'Вибрано 1 запис.|Вибрано :count записів.',

        'buttons' => [

            'select_all' => [
                'label' => 'Вибрати все :count',
            ],

            'deselect_all' => [
                'label' => 'Прибрати виділення з усіх',
            ],

        ],

    ],

];
