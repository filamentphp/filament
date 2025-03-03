<?php

return [

    'column_toggle' => [

        'heading' => 'Колоне',

    ],

    'columns' => [

        'actions' => [
            'label' => 'Акција|Акције',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Прикажи :count мање',
                'expand_list' => 'Прикажи :count vвишеiše',
            ],

            'more_list_items' => 'и још :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Одабери/поништи одабир свих ставки за групне радње.',
        ],

        'bulk_select_record' => [
            'label' => 'Одабери/поништи одабир ставке :key за групне радње.',
        ],

        'bulk_select_group' => [
            'label' => 'Одабери/поништи одабир групе :title за групне радње.',
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
            'all' => 'Сви :label',
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
            'label' => 'Заврши мењање редоследа записа',
        ],

        'enable_reordering' => [
            'label' => 'Мењање редоследа записа',
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

        'toggle_columns' => [
            'label' => 'Прикажи/сакриј колоне',
        ],

    ],

    'empty' => [

        'heading' => 'Нема :model',

        'description' => 'Креирај :model да бисте започели.',

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
        ],

        'trashed' => [

            'label' => 'Обрисани записи',

            'only_trashed' => 'Само обрисани записи',

            'with_trashed' => 'Са обрисаним записима',

            'without_trashed' => 'Без обрисаних записа',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Групиши према',
                'placeholder' => 'Групиши према',
            ],

            'direction' => [

                'label' => 'Смер груписања',

                'options' => [
                    'asc' => 'Растуће',
                    'desc' => 'Опадајуће',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Превуците и испустите записе у жељени редослед.',

    'selection_indicator' => [

        'selected_count' => '1 одабрани запис|:count одабраних записа',

        'actions' => [

            'select_all' => [
                'label' => 'Одабери свих :count',
            ],

            'deselect_all' => [
                'label' => 'Поништи одабир свих',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Сортирај према',
            ],

            'direction' => [

                'label' => 'Смер сортирања',

                'options' => [
                    'asc' => 'Растуће',
                    'desc' => 'Опадајуће',
                ],

            ],

        ],

    ],

];
