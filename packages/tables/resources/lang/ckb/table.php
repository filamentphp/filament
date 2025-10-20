<?php

return [

    'column_manager' => [

        'heading' => 'ستوونەکان',

        'actions' => [

            'apply' => [
                'label' => 'جێبەجێکردنی ستونەکان',
            ],

            'reset' => [
                'label' => 'ڕێکخستنەوە',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'کردار|کردارەکان',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'پیشاندانی :count کەمتر',
                'expand_list' => 'پیشاندانی :count زیاتر',
            ],

            'more_list_items' => 'هەروەها :count زیاتر',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'هەڵبژاردن/لابردنی هەڵبژاردنەکان بۆ هەموو تۆمارەکان بۆ کۆمەڵەی کردارەکان.',
        ],

        'bulk_select_record' => [
            'label' => 'هەڵبژاردن/لابردنی هەڵبژێردراوەژان بۆ :key بۆ کۆمەڵەی کردارەکان.',
        ],

        'bulk_select_group' => [
            'label' => 'هەڵبژاردن/لابردنی گرووپی :title بۆ کۆمەڵەی کردارەکان.',
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
            'label' => 'پاڵاوتن',
        ],

        'group' => [
            'label' => 'کۆمەڵ',
        ],

        'open_bulk_actions' => [
            'label' => 'کۆمەڵی کردارەکان',
        ],

        'column_manager' => [
            'label' => 'پشاندان/لابردنی ستوونەکان',
        ],

    ],

    'empty' => [

        'heading' => 'هیچ تۆمارێکی :model بوونی نییە.',

        'description' => 'تۆمارێکی :model دروس بکە بۆ دەستپێکردن.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'جێبەجێکردنی پاڵاوتنەکان',
            ],

            'remove' => [
                'label' => 'لابردنی پاڵاوتن',
            ],

            'remove_all' => [
                'label' => 'لابردنی هەموو پاڵاوتنەکان',
                'tooltip' => 'لابردنی هەموو پاڵاوتنەکان',
            ],

            'reset' => [
                'label' => 'ڕێکخسنتەوە',
            ],

        ],

        'heading' => 'پاڵاوتنەکان',

        'indicator' => 'پاڵاوتنە چالاککراوەکان',

        'multi_select' => [
            'placeholder' => 'هەموو',
        ],

        'select' => [

            'placeholder' => 'هەموو',

            'relationship' => [
                'empty_option_label' => 'هیچ',
            ],

        ],

        'trashed' => [

            'label' => 'تۆمارە سڕاوەکان',

            'only_trashed' => 'تەنها تۆمارە سڕاوەکان',

            'with_trashed' => 'لەگەل تۆمارە سڕاوەکان',

            'without_trashed' => 'بەبێ تۆمارە سڕاوەکان',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'کۆمەڵکردن بە',
            ],

            'direction' => [

                'label' => 'ئاڕاستەی کۆمەڵکردن',

                'options' => [
                    'asc' => 'کەم بۆ زۆر',
                    'desc' => 'زۆر بۆ کەم',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'تۆمارەکان هەڵبگرە و ڕیزیان بکە.',

    'selection_indicator' => [

        'selected_count' => '1 ڕیز هەڵبژێردراوە|:count ڕیز هەڵبژێردراوە',

        'actions' => [

            'select_all' => [
                'label' => 'هەڵبژاردنی هەموو :count',
            ],

            'deselect_all' => [
                'label' => 'هەڵنەبژاردنی هەموو',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'کۆمەڵکردن بە',
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

    'default_model_label' => 'تۆمار',

];
