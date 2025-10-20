<?php

return [

    'column_manager' => [

        'heading' => 'Колони',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Скрий :count повече',
                'expand_list' => 'Покажи :count повече',
            ],

            'more_list_items' => 'и още :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Изберете/премахнете избора на всички записи на тази страница.',
        ],

        'bulk_select_record' => [
            'label' => 'Изберете/премахнете избора на :key за масови действия.',
        ],

        'bulk_select_group' => [
            'label' => 'Изберете/премахнете избора на група :title за масови действия.',
        ],

        'search' => [
            'label' => 'Търси',
            'placeholder' => 'Търси',
            'indicator' => 'Търсене...',
        ],

    ],

    'summary' => [

        'heading' => 'Обобщение',

        'subheadings' => [
            'all' => 'Всички :label',
            'group' => 'Обебщение на :group',
            'page' => 'Текуща страница',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Средно',
            ],

            'count' => [
                'label' => 'Брой',
            ],

            'sum' => [
                'label' => 'Сума',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Приключи пренареждането',
        ],

        'enable_reordering' => [
            'label' => 'Пренареди',
        ],

        'filter' => [
            'label' => 'Филтрирай',
        ],

        'group' => [
            'label' => 'Групирай',
        ],

        'open_bulk_actions' => [
            'label' => 'Отвори масови действия',
        ],

        'column_manager' => [
            'label' => 'Превключи колони',
        ],

    ],

    'empty' => [

        'heading' => 'Няма :model',

        'description' => 'Създай нов :model за да започнеш.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Приложи',
            ],

            'remove' => [
                'label' => 'Премахни',
            ],

            'remove_all' => [
                'label' => 'Премахни всички',
                'tooltip' => 'Премахни всички филтри',
            ],

            'reset' => [
                'label' => 'Нулирай',
            ],

        ],

        'heading' => 'Филтри',

        'indicator' => 'Филтриране',

        'multi_select' => [
            'placeholder' => 'Всички',
        ],

        'select' => [
            'placeholder' => 'Избери',
        ],

        'trashed' => [

            'label' => 'Изтрити записи',

            'only_trashed' => 'Само изтрити записи',

            'with_trashed' => 'С изтрити записи',

            'without_trashed' => 'Без изтрити записи',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Групирай по',
                'placeholder' => 'Избери поле за групиране',
            ],

            'direction' => [

                'label' => 'Посока на групиране',

                'options' => [
                    'asc' => 'Възходящо',
                    'desc' => 'Низходящо',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Премествай записите с мишката.',

    'selection_indicator' => [

        'selected_count' => '1 запис избран|:count записа избрани',

        'actions' => [

            'select_all' => [
                'label' => 'Избери всички :count',
            ],

            'deselect_all' => [
                'label' => 'Премахнете избора на всички',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Сортирай по',
            ],

            'direction' => [

                'label' => 'Посока на сортиране',

                'options' => [
                    'asc' => 'Възходящо',
                    'desc' => 'Низходящо',
                ],

            ],

        ],

    ],

];
