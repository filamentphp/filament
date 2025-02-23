<?php

return [

    'column_toggle' => [

        'heading' => 'Սյունակներ',

    ],

    'columns' => [

        'actions' => [
            'label' => 'Գործողություն|Գործողություններ',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Ցույց տալ :count-ից քիչ',
                'expand_list' => 'Ցույց տալ :count-ից շատ',
            ],

            'more_list_items' => 'Եվ ևս :countը',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Ընտրել/չընտրել բոլոր տարրերը զանգվածային գործողությունների համար։',
        ],

        'bulk_select_record' => [
            'label' => 'Ընտրել/չընտրել :key նյութը զանգվածային գործողությունների համար։',
        ],

        'bulk_select_group' => [
            'label' => 'Ընտրել/չընտրել :title խումբը զանգվածային գործողությունների համար։',
        ],

        'search' => [
            'label' => 'Որոնել',
            'placeholder' => 'Որոնել',
            'indicator' => 'Որոնել',
        ],

    ],

    'summary' => [

        'heading' => 'Ամփոփում',

        'subheadings' => [
            'all' => 'Ամբողջ :labelը',
            'group' => ':group-ի ամփոփումը',
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
            'label' => 'Ավարտել վերադասավորումը',
        ],

        'enable_reordering' => [
            'label' => 'Միացնել վերադասավորումը',
        ],

        'filter' => [
            'label' => 'Ֆիլտր',
        ],

        'group' => [
            'label' => 'Խումբ',
        ],

        'open_bulk_actions' => [
            'label' => 'Բացել գործողություններ',
        ],

        'toggle_columns' => [
            'label' => 'Փոխել սյունակները',
        ],

    ],

    'empty' => [

        'heading' => ':model չկան',

        'description' => 'Սկզբի համար ստեղծեք :model։',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Կիրառել ֆիլտրերը',
            ],

            'remove' => [
                'label' => 'Հեռացնել ֆիլտրերը',
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
        ],

        'trashed' => [

            'label' => 'Ջնջված գրառումներ',

            'only_trashed' => 'Միայն ջնջված գրառումները',

            'with_trashed' => 'Ջնջված գրառումներով',

            'without_trashed' => 'Առանց ջնջված գրառումների',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Խմբավորել ըստ',
                'placeholder' => 'Խմբավորել ըստ',
            ],

            'direction' => [

                'label' => 'Խմբի ուղղություն',

                'options' => [
                    'asc' => 'Ըստ աճման',
                    'desc' => 'Ըստ նվազման',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Քաշեք գրառումները ցանկալի հերթականությամբ։',

    'selection_indicator' => [

        'selected_count' => '1 ընտրված գրառում|:count ընտրված գրառումներ',

        'actions' => [

            'select_all' => [
                'label' => 'Ընտրել ամբողջ :count-ը',
            ],

            'deselect_all' => [
                'label' => 'Ապաընտրել բոլորը',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Դասավորել ըստ',
            ],

            'direction' => [

                'label' => 'Տեսակավորման ուղղություն',

                'options' => [
                    'asc' => 'Աճող',
                    'desc' => 'Նվազող',
                ],

            ],

        ],

    ],

];
