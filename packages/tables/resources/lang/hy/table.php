<?php

return [

    'column_manager' => [

        'heading' => 'Սյուներ',

        'actions' => [

            'apply' => [
                'label' => 'Կիրառել սյուները',
            ],

            'reset' => [
                'label' => 'Վերականգնել',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Գործողություն|Գործողություններ',
        ],

        'select' => [

            'loading_message' => 'Բեռնում...',
            'no_search_results_message' => 'Ոչ մի տարբերակ չի համապատասխանում ձեր որոնմանը։',
            'placeholder' => 'Ընտրեք տարբերակ',
            'searching_message' => 'Որոնում...',
            'search_prompt' => 'Մուտքագրեք որոնման համար...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Ցույց տալ :count-ով քիչ',
                'expand_list' => 'Ցույց տալ :count-ով ավելին',
            ],

            'more_list_items' => 'և ևս :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Ընտրել/հեռացնել բոլոր տարրերը խմբային գործողությունների համար։',
        ],

        'bulk_select_record' => [
            'label' => 'Ընտրել/հեռացնել :key տարրը խմբային գործողությունների համար։',
        ],

        'bulk_select_group' => [
            'label' => 'Ընտրել/հեռացնել :title խումբը խմբային գործողությունների համար։',
        ],

        'search' => [
            'label' => 'Որոնում',
            'placeholder' => 'Որոնում',
            'indicator' => 'Որոնում',
        ],

    ],

    'summary' => [

        'heading' => 'Ամփոփում',

        'subheadings' => [
            'all' => 'Բոլոր :label',
            'group' => ':group ամփոփում',
            'page' => 'Այս էջը',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Միջին',
            ],

            'count' => [
                'label' => 'Քանակ',
            ],

            'sum' => [
                'label' => 'Գումար',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Ավարտել գրառումների վերադասավորումը',
        ],

        'enable_reordering' => [
            'label' => 'Վերադասավորել գրառումները',
        ],

        'filter' => [
            'label' => 'Ֆիլտր',
        ],

        'group' => [
            'label' => 'Խումբ',
        ],

        'open_bulk_actions' => [
            'label' => 'Խմբային գործողություններ',
        ],

        'column_manager' => [
            'label' => 'Սյուների կառավարիչ',
        ],

    ],

    'empty' => [

        'heading' => 'Ոչ մի :model',

        'description' => 'Սկսելու համար ստեղծեք :model։',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Կիրառել ֆիլտրերը',
            ],

            'remove' => [
                'label' => 'Հեռացնել ֆիլտրը',
            ],

            'remove_all' => [
                'label' => 'Հեռացնել բոլոր ֆիլտրերը',
                'tooltip' => 'Հեռացնել բոլոր ֆիլտրերը',
            ],

            'reset' => [
                'label' => 'Վերականգնել',
            ],

        ],

        'heading' => 'Ֆիլտրեր',

        'indicator' => 'Ակտիվ ֆիլտրեր',

        'multi_select' => [
            'placeholder' => 'Բոլորը',
        ],

        'select' => [

            'placeholder' => 'Բոլորը',

            'relationship' => [
                'empty_option_label' => 'Չկա',
            ],

        ],

        'trashed' => [

            'label' => 'Ջնջված գրառումներ',

            'only_trashed' => 'Միայն ջնջված գրառումները',
            'with_trashed' => 'Ջնջվածներով',
            'without_trashed' => 'Առանց ջնջվածների',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Խմբավորել ըստ',
            ],

            'direction' => [

                'label' => 'Խմբավորման ուղղություն',

                'options' => [
                    'asc' => 'Աճող',
                    'desc' => 'Նվազող',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Քաշեք և գցեք գրառումները՝ դասավորելու համար։',

    'selection_indicator' => [

        'selected_count' => 'Ընտրված է 1 գրառում|Ընտրված է :count գրառում',

        'actions' => [

            'select_all' => [
                'label' => 'Ընտրել բոլորը՝ :count',
            ],

            'deselect_all' => [
                'label' => 'Չընտրել բոլորն',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Դասավորել ըստ',
            ],

            'direction' => [

                'label' => 'Դասավորման ուղղություն',

                'options' => [
                    'asc' => 'Աճող',
                    'desc' => 'Նվազող',
                ],

            ],

        ],

    ],

    'default_model_label' => 'տվյալ',

];
