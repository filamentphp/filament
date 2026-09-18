<?php

return [

    'column_manager' => [
        'heading' => 'Safu wima',

        'actions' => [
            'apply' => [
                'label' => 'Tekeleza safu wima',
            ],

            'reorder' => [
                'label' => 'Panga upya safu wima',
            ],

            'reset' => [
                'label' => 'Weka upya',
            ],
        ],
    ],

    'columns' => [
        'actions' => [
            'label' => 'Kitendo|Vitendo',
        ],

        'icon' => [
            'boolean' => [
                'true' => 'Ndiyo',
                'false' => 'Hapana',
            ],
        ],

        'select' => [
            'loading_message' => 'Inapakia...',
            'no_options_message' => 'Hakuna chaguo zilizopo.',
            'no_search_results_message' => 'Hakuna chaguo linalolingana na utafutaji wako.',
            'placeholder' => 'Chagua chaguo',
            'searching_message' => 'Inatafuta...',
            'search_prompt' => 'Anza kuandika ili kutafuta...',
        ],

        'text' => [
            'actions' => [
                'collapse_list' => 'Ficha :count',
                'expand_list' => 'Onyesha :count zaidi',
            ],

            'more_list_items' => 'na :count zaidi',
        ],
    ],

    'fields' => [
        'bulk_select_page' => [
            'label' => 'Chagua/acha kuchagua vipengee vyote kwa vitendo vingi.',
        ],

        'bulk_select_record' => [
            'label' => 'Chagua/acha kuchagua kipengele :key kwa vitendo vingi.',
        ],

        'bulk_select_group' => [
            'label' => 'Chagua/ondoa uchaguzi wa kundi :title kwa vitendo vya wingi.',
        ],

        'search' => [
            'label' => 'Tafuta',
            'placeholder' => 'Tafuta',
            'indicator' => 'Tafuta',
        ],
    ],

    'summary' => [
        'heading' => 'Muhtasari',

        'subheadings' => [
            'all' => ':label zote',
            'group' => 'Muhtasari wa :group',
            'page' => 'Ukurasa huu',
        ],

        'summarizers' => [
            'average' => [
                'label' => 'Wastani',
            ],

            'count' => [
                'label' => 'Idadi',
            ],

            'sum' => [
                'label' => 'Jumla',
            ],
        ],
    ],

    'actions' => [
        'disable_reordering' => [
            'label' => 'Maliza kupangilia rekodi upya',
        ],

        'enable_reordering' => [
            'label' => 'Pangilia rekodi',
        ],

        'reorder_record' => [
            'label' => 'Panga upya kipengee :key',
        ],

        'filter' => [
            'label' => 'Chuja',
        ],

        'group' => [
            'label' => 'Kundi',
        ],

        'open_bulk_actions' => [
            'label' => 'Fungua vitendo vya wingi',
        ],

        'column_manager' => [
            'label' => 'Geuza safu',
        ],

        'toggle_record_content' => [
            'label' => 'Panua/kunja kipengee :key',
        ],
    ],

    'empty' => [
        'heading' => 'Hakuna rekodi zilizopatikana',
        'description' => 'Unda :model ili kuanza.',
    ],

    'filters' => [
        'actions' => [
            'apply' => [
                'label' => 'Tekeleza vichujio',
            ],

            'remove' => [
                'label' => 'Toa mchujo',
            ],

            'remove_all' => [
                'label' => 'Toa michujo yote',
                'tooltip' => 'Toa michujo yote',
            ],

            'reset' => [
                'label' => 'Weka upya michujo',
            ],
        ],

        'heading' => 'Vichujio',

        'indicator' => 'Michujo inayotumika',

        'multi_select' => [
            'placeholder' => 'Zote',
        ],

        'select' => [
            'placeholder' => 'Zote',

            'relationship' => [
                'empty_option_label' => 'Hakuna',
            ],
        ],

        'trashed' => [
            'label' => 'Rekodi zilizofutwa',
            'only_trashed' => 'Rekodi zilizofutwa pekee',
            'with_trashed' => 'Pamoja na rekodi zilizofutwa',
            'without_trashed' => 'Bila rekodi zilizofutwa',
        ],
    ],

    'grouping' => [
        'fields' => [
            'group' => [
                'label' => 'Panga kwa',
            ],

            'direction' => [
                'label' => 'Mwelekeo wa makundi',

                'options' => [
                    'asc' => 'Kupanda',
                    'desc' => 'Kushuka',
                ],
            ],
        ],
    ],

    'loading' => 'Inapakia...',

    'reorder_indicator' => 'Buruta na uangushe rekodi kwa mpangilio.',

    'result_count' => '{0} Hakuna matokeo|{1} kipengee :count|[2,*] vipengee :count',

    'selection_indicator' => [
        'selected_count' => 'Rekodi 1 imeshaguliwa|Rekodi :count zimeshaguliwa',

        'actions' => [
            'select_all' => [
                'label' => 'Chagua :count',
            ],

            'deselect_all' => [
                'label' => 'Acha kuchagua zote',
            ],
        ],
    ],

    'sorting' => [
        'fields' => [
            'column' => [
                'label' => 'Panga kwa',
            ],

            'direction' => [
                'label' => 'Panga mwelekeo',

                'options' => [
                    'asc' => 'Kupanda',
                    'desc' => 'Kushuka',
                ],
            ],
        ],
    ],

    'default_model_label' => 'rekodi',

];
