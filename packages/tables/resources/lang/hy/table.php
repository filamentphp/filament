<?php

return [

    'column_manager' => [

        'heading' => 'Սյունակներ',

        'position' => 'Դիրք :position՝ :count-ից։',

        'position_in_group' => 'Դիրք :position՝ :count-ից, :group խմբում։',

        'reordered' => ':column սյունակը տեղափոխվեց :position դիրք՝ :count-ից։',

        'reordered_deferred' => ':column սյունակը տեղափոխվեց :position դիրք՝ :count-ից։ Փոփոխությունները պահպանելու համար կիրառեք սյունակները։',

        'reordered_in_group' => ':column սյունակը տեղափոխվեց :position դիրք՝ :count-ից, :group խմբում։',

        'reordered_in_group_deferred' => ':column սյունակը տեղափոխվեց :position դիրք՝ :count-ից, :group խմբում։ Փոփոխությունները պահպանելու համար կիրառեք սյունակները։',

        'actions' => [

            'apply' => [
                'label' => 'Կիրառել սյունակները',
            ],

            'move_up' => [
                'label' => 'Բարձրացնել :column սյունակը',
            ],

            'move_down' => [
                'label' => 'Իջացնել :column սյունակը',
            ],

            'reorder' => [
                'label' => 'Վերադասավորել սյունակը',
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

        'editable' => [
            'saving' => 'Փոփոխությունը պահպանվում է...',
            'saved' => 'Փոփոխությունը պահպանվեց։',
            'failure' => 'Պահպանումը հնարավոր չեղավ հաստատել։ Կրկին փորձելուց առաջ թարմացրեք էջը։',
            'unavailable' => 'Այս տարրն այլևս հնարավոր չէ խմբագրել։',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'Այո',
                'false' => 'Ոչ',
            ],

        ],

        'select' => [

            'loading_message' => 'Բեռնում...',

            'no_options_message' => 'Տարբերակներ չկան',

            'no_search_results_message' => 'Ոչ մի տարբերակ չի համապատասխանում ձեր որոնմանը։',

            'placeholder' => 'Ընտրեք տարբերակ',

            'searching_message' => 'Որոնում...',

            'search_prompt' => 'Սկսեք մուտքագրել որոնման համար',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Ցույց տալ :count-ով պակաս',
                'expand_list' => 'Ցույց տալ ևս :count',
            ],

            'more_list_items' => 'և ևս :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Ընտրել/ապանշել բոլոր տարրերը խմբային գործողությունների համար։',
        ],

        'bulk_select_record' => [
            'label' => 'Ընտրել/ապանշել :key տարրը խմբային գործողությունների համար։',
        ],

        'bulk_select_group' => [
            'label' => 'Ընտրել/ապանշել :title խումբը խմբային գործողությունների համար։',
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

        'reorder_record' => [
            'label' => 'Վերադասավորել :key տարրը',
        ],

        'move_record_up' => [
            'label' => 'Բարձրացնել :label',
        ],

        'move_record_down' => [
            'label' => 'Իջացնել :label',
        ],

        'filter' => [
            'label' => 'Զտել',
        ],

        'group' => [
            'label' => 'Խումբ',
        ],

        'open_bulk_actions' => [
            'label' => 'Խմբային գործողություններ',
        ],

        'column_manager' => [
            'label' => 'Սյունակների կառավարիչ',
        ],

        'toggle_record_content' => [
            'label' => 'Ընդարձակել/ծալել :key տարրը',
        ],

    ],

    'empty' => [

        'heading' => ':model չկան',

        'description' => 'Սկսելու համար ստեղծեք :model։',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Կիրառել զտիչները',
            ],

            'remove' => [
                'label' => 'Հեռացնել զտիչը',
            ],

            'remove_all' => [
                'label' => 'Հեռացնել բոլոր զտիչները',
                'tooltip' => 'Հեռացնել բոլոր զտիչները',
            ],

            'reset' => [
                'label' => 'Վերականգնել',
            ],

        ],

        'heading' => 'Զտիչներ',

        'indicator' => 'Ակտիվ զտիչներ',

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

    'loading' => 'Բեռնում...',

    'reorder_indicator' => 'Քաշեք և գցեք գրառումները՝ դասավորելու համար։',

    'reordering' => [
        'position' => ':label, դիրք :position՝ :count-ից։',
        'moved' => ':label տեղափոխվեց :position դիրք՝ :count-ից։',
    ],

    'result_count' => '{0} Արդյունքներ չկան|{1} :count արդյունք|[2,*] :count արդյունք',

    'selection_indicator' => [

        'selected_count' => 'Ընտրված է 1 գրառում|Ընտրված է :count գրառում',

        'actions' => [

            'select_all' => [
                'label' => 'Ընտրել բոլորը՝ :count',
            ],

            'deselect_all' => [
                'label' => 'Ապանշել բոլորը',
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

    'default_model_label' => 'գրառում',

];
