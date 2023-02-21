<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'na :count zaidi',
        ],

        'messages' => [
            'copied' => 'Imeigwa',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Chagua/acha kuchagua vipengee vyote kwa vitendo vingi.',
        ],

        'bulk_select_record' => [
            'label' => 'Chagua/acha kuchagua kipengele :key kwa vitendo vingi.',
        ],

        'search_query' => [
            'label' => 'Tafuta',
            'placeholder' => 'Tafuta',
        ],

    ],

    'pagination' => [

        'label' => 'Urambazaji wa kurasa',

        'overview' => 'Onesha :first mpaka :last ya :total ya matokeo',

        'fields' => [

            'records_per_page' => [

                'label' => 'kwa kurasa',

                'options' => [
                    'all' => 'Zote',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Nenda kwenye kurasa :page',
            ],

            'next' => [
                'label' => 'Mbele',
            ],

            'previous' => [
                'label' => 'Nyuma',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Maliza kupangilia rekodi upya',
        ],

        'enable_reordering' => [
            'label' => 'Pangilia rekodi',
        ],

        'filter' => [
            'label' => 'Chuja',
        ],

        'open_actions' => [
            'label' => 'Fungua matendo',
        ],

        'toggle_columns' => [
            'label' => 'Geuza safu',
        ],

    ],

    'empty' => [
        'heading' => 'Hakuna rekodi zilizopatikana',
    ],

    'filters' => [

        'buttons' => [

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

        'indicator' => 'Michujo inayotumika',

        'multi_select' => [
            'placeholder' => 'Zote',
        ],

        'select' => [
            'placeholder' => 'Zote',
        ],

        'trashed' => [

            'label' => 'Rekodi zilizofutwa',

            'only_trashed' => 'Rekodi zilizofutwa pekee',

            'with_trashed' => 'Pamoja na rekodi zilizofutwa',

            'without_trashed' => 'Bila rekodi zilizofutwa',

        ],

    ],

    'reorder_indicator' => 'Buruta na uangushe rekodi kwa mpangilio.',

    'selection_indicator' => [

        'selected_count' => 'Rekodi 1 imeshaguliwa .|Rekodi :count zimeshaguliwa.',

        'buttons' => [

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

];
