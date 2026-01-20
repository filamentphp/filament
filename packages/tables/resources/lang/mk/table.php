<?php

return [

    'column_manager' => [

        'heading' => 'Колони',

        'actions' => [

            'apply' => [
                'label' => 'Примени колони',
            ],

            'reset' => [
                'label' => 'Ресетирај',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Акција|Акции',
        ],

        'select' => [

            'loading_message' => 'Се вчитува...',

            'no_options_message' => 'Нема достапни опции.',

            'no_search_results_message' => 'Нема опции кои се совпаѓаат со вашето пребарување.',

            'placeholder' => 'Избери опција',

            'searching_message' => 'Се пребарува...',

            'search_prompt' => 'Започнете да пишувате за пребарување...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Прикажи :count помалку',
                'expand_list' => 'Прикажи :count повеќе',
            ],

            'more_list_items' => 'и :count повеќе',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Избери/одбери сите ставки за масовни акции.',
        ],

        'bulk_select_record' => [
            'label' => 'Избери/одбери ставка :key за масовни акции.',
        ],

        'bulk_select_group' => [
            'label' => 'Избери/одбери група :title за масовни акции.',
        ],

        'search' => [
            'label' => 'Пребарај',
            'placeholder' => 'Пребарај',
            'indicator' => 'Пребарај',
        ],

    ],

    'summary' => [

        'heading' => 'Резиме',

        'subheadings' => [
            'all' => 'Сите :label',
            'group' => ':group резиме',
            'page' => 'Оваа страница',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Просек',
            ],

            'count' => [
                'label' => 'Број',
            ],

            'sum' => [
                'label' => 'Збир',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Заврши преуредување на записи',
        ],

        'enable_reordering' => [
            'label' => 'Преуреди записи',
        ],

        'filter' => [
            'label' => 'Филтер',
        ],

        'group' => [
            'label' => 'Група',
        ],

        'open_bulk_actions' => [
            'label' => 'Масовни акции',
        ],

        'column_manager' => [
            'label' => 'Менаџер на колони',
        ],

    ],

    'empty' => [

        'heading' => 'Нема :model',

        'description' => 'Креирај :model за да започнеш.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Примени филтри',
            ],

            'remove' => [
                'label' => 'Отстрани филтер',
            ],

            'remove_all' => [
                'label' => 'Отстрани сите филтри',
                'tooltip' => 'Отстрани сите филтри',
            ],

            'reset' => [
                'label' => 'Ресетирај',
            ],

        ],

        'heading' => 'Филтри',

        'indicator' => 'Активни филтри',

        'multi_select' => [
            'placeholder' => 'Сите',
        ],

        'select' => [

            'placeholder' => 'Сите',

            'relationship' => [
                'empty_option_label' => 'Нема',
            ],

        ],

        'trashed' => [

            'label' => 'Избришани записи',

            'only_trashed' => 'Само избришани записи',

            'with_trashed' => 'Со избришани записи',

            'without_trashed' => 'Без избришани записи',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Групирај по',
            ],

            'direction' => [

                'label' => 'Насока на групирање',

                'options' => [
                    'asc' => 'Растечки',
                    'desc' => 'Опаѓачки',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Влечи и спушти ги записите по редослед.',

    'selection_indicator' => [

        'selected_count' => '1 запис избран|:count записи избрани',

        'actions' => [

            'select_all' => [
                'label' => 'Избери сите :count',
            ],

            'deselect_all' => [
                'label' => 'Одбери сите',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Сортирај по',
            ],

            'direction' => [

                'label' => 'Насока на сортирање',

                'options' => [
                    'asc' => 'Растечки',
                    'desc' => 'Опаѓачки',
                ],

            ],

        ],

    ],

    'default_model_label' => 'запис',

];
