<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'і :count ще',
        ],

    ],

    'fields' => [

        'search' => [
            'label' => 'Пошук',
            'placeholder' => 'Пошук',
        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Зберегти порядок',
        ],

        'enable_reordering' => [
            'label' => 'Змінити порядок',
        ],

        'filter' => [
            'label' => 'Фільтр',
        ],

        'open_bulk_actions' => [
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

        'actions' => [

            'remove' => [
                'label' => 'Видалити фільтр',
            ],

            'remove_all' => [
                'label' => 'Очистити фільтри',
                'tooltip' => 'Очистити фільтри',
            ],

            'reset' => [
                'label' => 'Скинути фільтри',
            ],

        ],

        'indicator' => 'Активні фільтри',

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

        'selected_count' => 'Вибрано 1 запис|Вибрано :count записів',

        'actions' => [

            'select_all' => [
                'label' => 'Вибрати все :count',
            ],

            'deselect_all' => [
                'label' => 'Прибрати виділення з усіх',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Сортування',
            ],

            'direction' => [

                'label' => 'Напрямок сортування',

                'options' => [
                    'asc' => 'За зростанням',
                    'desc' => 'За зменшенням',
                ],

            ],

        ],

    ],

];
