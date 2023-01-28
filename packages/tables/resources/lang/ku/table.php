<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'وە :count زیاتر',
        ],

        'messages' => [
            'copied' => 'لەبەرگیرا',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'گەڕان',
            'placeholder' => 'گەڕان',
        ],

    ],

    'pagination' => [

        'label' => 'ڕێنوێیی پەڕەکردن',

        'overview' => 'پیشاندان :first بۆ :last لە :total ئەنجام',

        'fields' => [

            'records_per_page' => [
                'label' => 'بۆ هەر پەڕەیەک',
                'options' => [
                    'all' => 'هەموو',
                ],
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'بڕۆ بۆ پەڕەی :page',
            ],

            'next' => [
                'label' => 'دواتر',
            ],

            'previous' => [
                'label' => 'پێشوو',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'کۆتایی بە ڕێکخستن ب‌هێنە',
        ],

        'enable_reordering' => [
            'label' => 'چالاک کردنی ڕێکخستن',
        ],

        'filter' => [
            'label' => 'فلتەر',
        ],

        'open_actions' => [
            'label' => 'کردنەوەی کارەکان',
        ],

        'toggle_columns' => [
            'label' => 'ڕەوشتی خانە',
        ],

    ],

    'empty' => [
        'heading' => 'هیچ تۆمارێک نەدۆزرایەوە',
    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'سرینەوەی فلتەر',
            ],

            'remove_all' => [
                'label' => 'سرینەوەی هەموو فلتەرەکان',
                'tooltip' => 'سرینەوەی هەوو فلتەرەکان',
            ],

            'reset' => [
                'label' => 'لابردنی هەموو فلتەر',
            ],

        ],

        'indicator' => 'فلتەری چالاک',

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

            'without_trashed' => 'جگە لە تۆمارە سڕدراوەکان',

        ],

    ],

    'reorder_indicator' => 'ڕاکێشان و فڕێدانی تۆمارەکان بۆ ڕیزکردن.',

    'selection_indicator' => [

        'selected_count' => '١ تۆمار دیاری کراوە.|:count تۆمار دیاری کراوە.',

        'buttons' => [

            'select_all' => [
                'label' => 'دیاریکردنی هەموو :count تۆمارەکان',
            ],

            'deselect_all' => [
                'label' => 'لابردنی دیاریکردنی هەموو تۆمارەکان',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'ریزبەندی',
            ],

            'direction' => [

                'label' => 'جۆری ڕیزبەندی',

                'options' => [
                    'asc' => 'کەم بۆ زۆر',
                    'desc' => 'زۆر بۆ کەم',
                ],

            ],

        ],

    ],

];
