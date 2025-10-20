<?php

return [

    'column_manager' => [

        'heading' => 'Столбцы',

        'actions' => [

            'apply' => [
                'label' => 'Применить столбцы',
            ],

            'reset' => [
                'label' => 'Сбросить',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Действие|Действия',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Скрыть :count',
                'expand_list' => 'Показать еще :count',
            ],

            'more_list_items' => 'и :count еще',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Выбрать/снять все элементы для массовых действий.',
        ],

        'bulk_select_record' => [
            'label' => 'Выбрать/отменить :key для массовых действий.',
        ],

        'bulk_select_group' => [
            'label' => 'Выбрать/отменить сводку :title для массовых действий.',
        ],

        'search' => [
            'label' => 'Поиск',
            'placeholder' => 'Поиск',
            'indicator' => 'Поиск',
        ],

    ],

    'summary' => [

        'heading' => 'Сводка',

        'subheadings' => [
            'all' => 'Все :label',
            'group' => 'Cводка :group',
            'page' => 'Эта страница',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Среднее',
            ],

            'count' => [
                'label' => 'Кол.',
            ],

            'sum' => [
                'label' => 'Сумма',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Сохранить порядок',
        ],

        'enable_reordering' => [
            'label' => 'Изменить порядок',
        ],

        'filter' => [
            'label' => 'Фильтр',
        ],

        'group' => [
            'label' => 'Группировать',
        ],

        'open_bulk_actions' => [
            'label' => 'Открыть действия',
        ],

        'column_manager' => [
            'label' => 'Переключить столбцы',
        ],

    ],

    'empty' => [

        'heading' => 'Не найдено :model',

        'description' => 'Создать :model для старта.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Применить фильтры',
            ],

            'remove' => [
                'label' => 'Удалить фильтр',
            ],

            'remove_all' => [
                'label' => 'Очистить фильтры',
                'tooltip' => 'Очистить фильтры',
            ],

            'reset' => [
                'label' => 'Сбросить',
            ],

        ],

        'heading' => 'Фильтры',

        'indicator' => 'Активные фильтры',

        'multi_select' => [
            'placeholder' => 'Все',
        ],

        'select' => [

            'placeholder' => 'Все',

            'relationship' => [
                'empty_option_label' => 'Нет',
            ],

        ],

        'trashed' => [

            'label' => 'Удаленные записи',

            'only_trashed' => 'Только удаленные записи',

            'with_trashed' => 'С удаленными записями',

            'without_trashed' => 'Без удаленных записей',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Группировать по',
                'placeholder' => 'Группировать по',
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

    'reorder_indicator' => 'Перетягивайте записи, чтобы изменить порядок.',

    'selection_indicator' => [

        'selected_count' => 'Выбрана 1 запись|Выбрано :count записей',

        'actions' => [

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
                'label' => 'Сортировка',
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

    'default_model_label' => 'запись',

];
