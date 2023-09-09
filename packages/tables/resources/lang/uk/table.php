<?php

return [
    'column_toggle' => [

        'heading' => 'Стовпці',

    ],
    'columns' => [

        'text' => [
            'more_list_items' => 'і :count ще',
        ],

    ],

    'fields' => [
        'bulk_select_page' => [
            'label' => 'Обрати/зняти всі елементи для масових дій.',
        ],

        'bulk_select_record' => [
            'label' => 'Обрати/скасувати :key для масових дій.',
        ],
        'search' => [
            'label' => 'Пошук',
            'placeholder' => 'Пошук',
            'indicator' => 'Пошук',
        ],

    ],

    'summary' => [

        'heading' => 'Підсумок',

        'subheadings' => [
            'all' => 'Всі :label',
            'group' => 'Підсумок :group ',
            'page' => 'Ця сторінка',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Середнє',
            ],

            'count' => [
                'label' => 'Кол.',
            ],

            'sum' => [
                'label' => 'Сума',
            ],

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
        'group' => [
            'label' => 'Групувати',
        ],
        'open_bulk_actions' => [
            'label' => 'Відкрити дії',
        ],

        'toggle_columns' => [
            'label' => 'Переключити стовпці',
        ],

    ],

    'empty' => [

        'heading' => 'Не знайдено :model',

        'description' => 'Створити :model для початку.',
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
                'label' => 'Скинути',
            ],

        ],

        'heading' => 'Фільтри',

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

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Групувати за',
                'placeholder' => 'Групувати за',
            ],

            'direction' => [

                'label' => 'Напрямок',

                'options' => [
                    'asc' => 'За зростанням',
                    'desc' => 'За спаданням',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Drag-n-drop порядок.',

    'selection_indicator' => [

        'selected_count' => 'Обрано 1 запис|Обрано :count записів',

        'actions' => [

            'select_all' => [
                'label' => 'Обрати все :count',
            ],

            'deselect_all' => [
                'label' => 'Прибрати виділення з усіх',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Сортувати за',
            ],

            'direction' => [

                'label' => 'Напрямок',

                'options' => [
                    'asc' => 'За зростанням',
                    'desc' => 'За спаданням',
                ],

            ],

        ],

    ],

];
