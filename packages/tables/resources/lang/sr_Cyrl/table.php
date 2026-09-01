<?php

return [

    'column_manager' => [

        'heading' => 'Колоне',

        'actions' => [

            'apply' => [
                'label' => 'Примени колоне',
            ],

            'reset' => [
                'label' => 'Поништи',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Акција|Акције',
        ],

        'select' => [

            'loading_message' => 'Учитавање...',

            'no_search_results_message' => 'Ниједна опција не се подудара.',

            'placeholder' => 'Изаберите опцију',

            'searching_message' => 'Претрага...',

            'search_prompt' => 'Започните унос за претрагу...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Прикажи :count мање',
                'expand_list' => 'Прикажи :count више',
            ],

            'more_list_items' => 'и још :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Изабери/поништи избор свих ставки за групне радње.',
        ],

        'bulk_select_record' => [
            'label' => 'Изабери/поништи изабране ставке :key за групне радње.',
        ],

        'bulk_select_group' => [
            'label' => 'Изабери/поништи изабране групе :title за групне радње.',
        ],

        'search' => [
            'label' => 'Претрага',
            'placeholder' => 'Претражи',
            'indicator' => 'Претражи',
        ],

    ],

    'summary' => [

        'heading' => 'Сажетак',

        'subheadings' => [
            'all' => 'Све :label',
            'group' => ':group сажетак',
            'page' => 'Ова страница',
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
            'label' => 'Онемогући промену редоследа записа',
        ],

        'enable_reordering' => [
            'label' => 'Промена редоследа записа',
        ],

        'filter' => [
            'label' => 'Филтер',
        ],

        'group' => [
            'label' => 'Група',
        ],

        'open_bulk_actions' => [
            'label' => 'Групне радње',
        ],

        'column_manager' => [
            'label' => 'Прикажи/сакриј колоне',
        ],

    ],

    'empty' => [

        'heading' => 'Нема :model',

        'description' => 'Створите :model да бисте почели.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Примени филтер',
            ],

            'remove' => [
                'label' => 'Уклони филтер',
            ],

            'remove_all' => [
                'label' => 'Уклони све филтере',
                'tooltip' => 'Уклони све филтере',
            ],

            'reset' => [
                'label' => 'Поништи',
            ],

        ],

        'heading' => 'Филтери',

        'indicator' => 'Активни филтери',

        'multi_select' => [
            'placeholder' => 'Све',
        ],

        'select' => [
            'placeholder' => 'Све',

            'relationship' => [
                'empty_option_label' => 'Ништа',
            ],
        ],

        'trashed' => [

            'label' => 'Избрисани записи',

            'only_trashed' => 'Само избрисани записи',

            'with_trashed' => 'Са избрисаним записима',

            'without_trashed' => 'Без избрисаних записа',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Групишите по',
            ],

            'direction' => [

                'label' => 'Смер груписања',

                'options' => [
                    'asc' => 'Узлазно',
                    'desc' => 'Силазно',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Повуци и испусти записе у редоследу.',

    'selection_indicator' => [

        'selected_count' => '1 изабрани запис|:count изабраних записа',

        'actions' => [

            'select_all' => [
                'label' => 'Изаберите све :count',
            ],

            'deselect_all' => [
                'label' => 'Означите све',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Сортирај по',
            ],

            'direction' => [

                'label' => 'Смер сортирања',

                'options' => [
                    'asc' => 'Узлазно',
                    'desc' => 'Силазно',
                ],

            ],

        ],

    ],

    'default_model_label' => 'запис',

];
