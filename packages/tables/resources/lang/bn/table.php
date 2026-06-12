<?php

return [

    'column_manager' => [

        'heading' => 'কলাম',

        'actions' => [

            'apply' => [
                'label' => 'কলামগুলো প্রয়োগ করুন',
            ],

            'reset' => [
                'label' => 'আগের অবস্থায় ফিরান',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'অ্যাকশন|অ্যাকশনসমূহ',
        ],

        'select' => [

            'loading_message' => 'লোড হচ্ছে...',

            'no_options_message' => 'কোন অপশন নেই।',

            'no_search_results_message' => 'আপনার অনুসন্ধানের সাথে মেলে এমন কিছু পাওয়া যায়নি।',

            'placeholder' => 'একটি অপশন নির্বাচন করুন',

            'searching_message' => 'অনুসন্ধান চলছে...',

            'search_prompt' => 'অনুসন্ধান শুরু করতে টাইপ করুন...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => ':countটি কম দেখান',
                'expand_list' => ':countটি বেশি দেখান',
            ],

            'more_list_items' => 'এবং আরো :countটি',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'একসাথে একাধিক কাজ করার জন্য সব আইটেম নির্বাচন/অনির্বাচন করুন।',
        ],

        'bulk_select_record' => [
            'label' => 'একসাথে একাধিক কাজের জন্য :key আইটেম নির্বাচন/অনির্বাচন করুন।',
        ],

        'bulk_select_group' => [
            'label' => 'একসাথে একাধিক কাজের জন্য :title গ্রুপ নির্বাচন/অনির্বাচন করুন।',
        ],

        'search' => [
            'label' => 'অনুসন্ধান',
            'placeholder' => 'খুঁজুন',
            'indicator' => 'অনুসন্ধান',
        ],

    ],

    'summary' => [

        'heading' => 'সারসংক্ষেপ',

        'subheadings' => [
            'all' => 'সব :label',
            'group' => ':group এর সারসংক্ষেপ',
            'page' => 'এই পৃষ্ঠা',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'গড়',
            ],

            'count' => [
                'label' => 'গণনা',
            ],

            'sum' => [
                'label' => 'সমষ্টি',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'রেকর্ড পুনর্বিন্যাস সম্পন্ন করুন',
        ],

        'enable_reordering' => [
            'label' => 'রেকর্ড পুনর্বিন্যাস করুন',
        ],

        'filter' => [
            'label' => 'ফিল্টার',
        ],

        'group' => [
            'label' => 'গ্রুপ',
        ],

        'open_bulk_actions' => [
            'label' => 'একসাথে একাধিক কাজ',
        ],

        'column_manager' => [
            'label' => 'কলাম ব্যবস্থাপক',
        ],

    ],

    'empty' => [

        'heading' => ':model পাওয়া যায়নি',

        'description' => 'শুরু করতে একটি :model তৈরি করুন।',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'ফিল্টার প্রয়োগ করুন',
            ],

            'remove' => [
                'label' => 'ফিল্টার সরান',
            ],

            'remove_all' => [
                'label' => 'সব ফিল্টার সরান',
                'tooltip' => 'সব ফিল্টার সরান',
            ],

            'reset' => [
                'label' => 'আগের অবস্থায় ফিরান',
            ],

        ],

        'heading' => 'ফিল্টার',

        'indicator' => 'সক্রিয় ফিল্টার',

        'multi_select' => [
            'placeholder' => 'সব',
        ],

        'select' => [

            'placeholder' => 'সব',

            'relationship' => [
                'empty_option_label' => 'কিছুই নয়',
            ],

        ],

        'trashed' => [

            'label' => 'মুছে ফেলা রেকর্ড',

            'only_trashed' => 'শুধু মুছে ফেলা রেকর্ডগুলো',

            'with_trashed' => 'মুছে ফেলা রেকর্ডগুলো সহ',

            'without_trashed' => 'মুছে ফেলা রেকর্ডগুলো ছাড়া',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'গ্রুপ করার ভিত্তি',
            ],

            'direction' => [

                'label' => 'গ্রুপের দিক',

                'options' => [
                    'asc' => 'উর্ধ্বক্রমে',
                    'desc' => 'নিম্নক্রমে',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'ররেকর্ডগুলো টেনে এনে ক্রম অনুযায়ী সাজান।',

    'selection_indicator' => [

        'selected_count' => '১টি রেকর্ড নির্বাচিত|:countটি রেকর্ড নির্বাচিত',

        'actions' => [

            'select_all' => [
                'label' => 'সব নির্বাচন করুন (:count)',
            ],

            'deselect_all' => [
                'label' => 'সব নির্বাচন বাতিল করুন',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'অনুযায়ী সাজান',
            ],

            'direction' => [

                'label' => 'সাজানোর দিক',

                'options' => [
                    'asc' => 'উর্ধ্বক্রমে',
                    'desc' => 'নিম্নক্রমে',
                ],

            ],

        ],

    ],

    'default_model_label' => 'রেকর্ড',

];
