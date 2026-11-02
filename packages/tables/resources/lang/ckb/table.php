<?php

return [

    'column_manager' => [

        'heading' => 'ستوونەکان',

        'position' => 'شوێنی :position لە :count.',

        'position_in_group' => 'شوێنی :position لە :count لەناو :group.',

        'reordered' => ':column گوازرایەوە بۆ شوێنی :position لە :count.',

        'reordered_deferred' => ':column گوازرایەوە بۆ شوێنی :position لە :count. ستوونەکان جێبەجێ بکە بۆ پاشەکەوتکردنی گۆڕانکارییەکانت.',

        'reordered_in_group' => ':column گوازرایەوە بۆ شوێنی :position لە :count لەناو :group.',

        'reordered_in_group_deferred' => ':column گوازرایەوە بۆ شوێنی :position لە :count لەناو :group. ستوونەکان جێبەجێ بکە بۆ پاشەکەوتکردنی گۆڕانکارییەکانت.',

        'actions' => [

            'apply' => [
                'label' => 'جێبەجێکردنی ستونەکان',
            ],

            'move_up' => [
                'label' => 'جوڵاندنی :column بۆ سەرەوە',
            ],

            'move_down' => [
                'label' => 'جوڵاندنی :column بۆ خوارەوە',
            ],

            'reorder' => [
                'label' => 'ڕیزکردنەوەی ستوون',
            ],

            'reset' => [
                'label' => 'ڕێکخستنەوە',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => '{1} کردار|[2,*] کردارەکان',
        ],

        'editable' => [
            'saving' => 'پاشەکەوتکردنی گۆڕانکاری...',
            'saved' => 'گۆڕانکارییەکە پاشەکەوت کرا.',
            'failure' => 'پاشەکەوتکردنەکە پشتڕاست نەکرایەوە. پێش هەوڵدانەوە پەڕەکە نوێ بکەرەوە.',
            'unavailable' => 'ئەم دانەیە چیتر ناتوانرێت دەستکاری بکرێت.',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'بەڵێ',
                'false' => 'نەخێر',
            ],

        ],

        'select' => [

            'loading_message' => 'بارکردن...',

            'no_options_message' => 'هیچ هەڵبژاردەیەک بەردەست نییە.',

            'no_search_results_message' => 'هیچ هەڵبژاردەیەک لەگەڵ گەڕانەکەت ناگونجێت.',

            'placeholder' => 'هەڵبژاردەیەک هەڵبژێرە',

            'searching_message' => 'گەڕان...',

            'search_prompt' => 'دەست بکە بە نووسین بۆ گەڕان',

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

        'reorder_record' => [
            'label' => 'ڕیزکردنەوەی دانەی :key',
        ],

        'move_record_up' => [
            'label' => 'جوڵاندنی :label بۆ سەرەوە',
        ],

        'move_record_down' => [
            'label' => 'جوڵاندنی :label بۆ خوارەوە',
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

        'toggle_record_content' => [
            'label' => 'کردنەوە/داخستنی دانەی :key',
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

    'loading' => 'بارکردن...',

    'reorder_indicator' => 'تۆمارەکان هەڵبگرە و ڕیزیان بکە.',

    'reordering' => [
        'position' => ':label، شوێنی :position لە :count.',
        'moved' => ':label گوازرایەوە بۆ شوێنی :position لە :count.',
    ],

    'result_count' => '{0} هیچ ئەنجامێک نییە|{1} :count ئەنجام|[2,*] :count ئەنجام',

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
