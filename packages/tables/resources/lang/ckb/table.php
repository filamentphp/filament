<?php

return [

    'column_toggle' => [

        'heading' => 'ستوونەکان',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'وە :count ی زیاتر',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'دیاریکردن/لابردنی دیاریکردنەکان بۆ هەموو تۆمارەکان بۆ کۆمەڵەی کردارەکان.',
        ],

        'bulk_select_record' => [
            'label' => 'دیاریکردن/لابردنی دیاریکراوەکان بۆ :key بۆ کۆمەڵەی کردارەکان.',
        ],

        'search' => [
            'label' => 'گەڕان',
            'placeholder' => 'گەڕان',
            'indicator' => 'گەڕان',
        ],

    ],

    'summary' => [

        'heading' => 'پوختە',

        'subheadings' => [
            'all' => 'هەموو :label',
            'group' => ':group پوختە',
            'page' => 'ئەم پەڕەیە',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'تێکڕا',
            ],

            'count' => [
                'label' => 'ژماردەکان',
            ],

            'sum' => [
                'label' => 'کۆی گشتی',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'تەواوکردنی ڕێکخستنی تۆمارەکان',
        ],

        'enable_reordering' => [
            'label' => 'ڕێکخستنی تۆمارەکان',
        ],

        'filter' => [
            'label' => 'فلتەر',
        ],

        'group' => [
            'label' => 'کۆمەڵ',
        ],

        'open_bulk_actions' => [
            'label' => 'کۆمەڵی کردارەکان',
        ],

        'toggle_columns' => [
            'label' => 'پشاندان/لابردنی ستوونەکان',
        ],

    ],

    'empty' => [

        'heading' => 'هیچ تۆمارێکی :model بوونی نییە.',

        'description' => 'تۆمارێکی :model دروس بکە بۆ دەستپێکردن.',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'سڕینەوەی فلتەر',
            ],

            'remove_all' => [
                'label' => 'سڕینەوەی هەموو فلتەرەکان',
                'tooltip' => 'سڕینەوەی هەموو فلتەرەکان',
            ],

            'reset' => [
                'label' => 'دۆخی سەرەتا',
            ],

        ],

        'heading' => 'فلتەرەکان',

        'indicator' => 'فلتەرە چالاککراوەکان',

        'multi_select' => [
            'placeholder' => 'هەموو',
        ],

        'select' => [
            'placeholder' => 'هەموو',
        ],

        'trashed' => [

            'label' => 'تۆمارە سڕدراوەکان',

            'only_trashed' => 'تەنها تۆمارە سڕدراوەکان',

            'with_trashed' => 'لەگەل تۆمارە سڕدراوەکان',

            'without_trashed' => 'بەبێ تۆمارە سڕدراوەکان',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'کۆمەڵ کردن بە',
                'placeholder' => 'کۆمەڵ کردن بە',
            ],

            'direction' => [

                'label' => 'ئاڕاستەی کۆمەڵ کردن',

                'options' => [
                    'asc' => 'کەم بۆ زۆر',
                    'desc' => 'زۆر بۆ کەم',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Drag and drop the records into order.',

    'selection_indicator' => [

        'selected_count' => '1 record selected|:count records selected',

        'actions' => [

            'select_all' => [
                'label' => 'Select all :count',
            ],

            'deselect_all' => [
                'label' => 'Deselect all',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sort by',
            ],

            'direction' => [

                'label' => 'Sort direction',

                'options' => [
                    'asc' => 'Ascending',
                    'desc' => 'Descending',
                ],

            ],

        ],

    ],

];
