<?php

return [

    'columns' => [

        'color' => [

            'messages' => [
                'copied' => 'Скопійовано',
            ],

        ],

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

                'options' => [
                    'all' => 'Всі',
                ],

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
        'heading' => 'не знайдено записів',
    ],

    'filters' => [

        'buttons' => [

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

            'label' => 'Віддалені записи',

            'only_trashed' => 'Тільки видалені записи',

            'with_trashed' => 'З віддаленими записами',

            'without_trashed' => 'Без віддалених записів',

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
