<?php

return [

    'columns' => [

        'color' => [

            'messages' => [
                'copied' => 'Скопировано',
            ],

        ],

        'tags' => [
            'more' => 'и :count еще',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Поиск',
            'placeholder' => 'Поиск',
        ],

    ],

    'pagination' => [

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

        'disable_reordering' => [
            'label' => 'Сохранить порядок',
        ],

        'enable_reordering' => [
            'label' => 'Изменить порядок',
        ],

        'filter' => [
            'label' => 'Фильтр',
        ],

        'open_actions' => [
            'label' => 'Открыть действия',
        ],

        'toggle_columns' => [
            'label' => 'Переключить столбцы',
        ],

    ],

    'empty' => [
        'heading' => 'Не найдено записей',
    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Удалить фильтр',
            ],

            'remove_all' => [
                'label' => 'Отчистить фильтры',
                'tooltip' => 'Отчистить фильтры',
            ],

            'reset' => [
                'label' => 'Сбросить фильтры',
            ],

        ],

        'indicator' => 'Активные фильтры',

        'multi_select' => [
            'placeholder' => 'Все',
        ],

        'select' => [
            'placeholder' => 'Все',
        ],

        'trashed' => [

            'label' => 'Удаленные записи',

            'only_trashed' => 'Только удаленные записи',

            'with_trashed' => 'С удаленными записями',

            'without_trashed' => 'Без удаленных записей',

        ],

    ],

    'reorder_indicator' => 'Drag-n-drop порядок записей.',

    'selection_indicator' => [

        'selected_count' => 'Выбрана 1 запись.|Выбрано :count записей.',

        'buttons' => [

            'select_all' => [
                'label' => 'Выбрать всё :count',
            ],

            'deselect_all' => [
                'label' => 'Убрать выделение со всех',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Сортировать по',
            ],

            'direction' => [

                'label' => 'Направление',

                'options' => [
                    'asc' => 'По возрастанию',
                    'desc' => 'По убыванию',
                ],

            ],

        ],

    ],

];
