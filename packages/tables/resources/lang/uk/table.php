<?php

return [

    'column_manager' => [

        'heading' => 'Стовпці',

        'actions' => [

            'apply' => [
                'label' => 'Застосувати стовпці',
            ],

            'reset' => [
                'label' => 'Скинути',
            ],
        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Дія|Дії',
        ],

        'select' => [

            'loading_message' => 'Завантаження...',

            'no_search_results_message' => 'Немає варіантів, що відповідають вашому пошуку.',

            'placeholder' => 'Виберіть варіант',

            'searching_message' => 'Пошук...',

            'search_prompt' => 'Почніть вводити текст для пошуку...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Показати :count менше',
                'expand_list' => 'Показати :count більше',
            ],

            'more_list_items' => 'і :count ще',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Обрати/зняти всі елементи для масових дій.',
        ],

        'bulk_select_record' => [
            'label' => 'Обрати/зняти елемент :key для масових дій.',
        ],

        'bulk_select_group' => [
            'label' => 'Обрати/зняти елемент групу :title для масових дій.',
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

        'column_manager' => [
            'label' => 'Переключити стовпці',
        ],

    ],

    'empty' => [

        'heading' => 'Не знайдено :model',

        'description' => 'Створити :model для початку.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Застосувати фільтри',
            ],

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

            'relationship' => [
                'empty_option_label' => 'Немає',
            ],

        ],

        'trashed' => [

            'label' => 'Видалені записи',

            'only_trashed' => 'Тільки видалені записи',

            'with_trashed' => 'З видаленими записами',

            'without_trashed' => 'Без видалених записів',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Групувати за',
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

    'reorder_indicator' => 'Перетягуйте елементи, щоб змінити порядок.',

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

    'default_model_label' => 'запис',

];
