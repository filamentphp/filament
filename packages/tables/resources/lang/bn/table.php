<?php

return [

    'columns' => [

        'text' => [
            'more_list_items' => 'এবং আরো :count',
        ],

    ],

    'fields' => [

        'search' => [
            'label' => 'খুঁজুন',
            'placeholder' => 'খুঁজুন',
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
            'label' => 'অনুসন্ধান করুন',
        ],

        'open_bulk_actions' => [
            'label' => 'কার্যক্রম গুলো দেখুন',
        ],

        'column_manager' => [
            'label' => 'কলাম টগল করুন',
        ],

    ],

    'empty' => [
        'heading' => 'রেকর্ড পাওয়া যায়নি',
    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'অনুসন্ধান সরান',
            ],

            'remove_all' => [
                'label' => 'সব অনুসন্ধান সরান',
                'tooltip' => 'সব অনুসন্ধান সরান',
            ],

            'reset' => [
                'label' => 'অনুসন্ধান সরান',
            ],

        ],

        'indicator' => 'সক্রিয় অনুসন্ধান',

        'multi_select' => [
            'placeholder' => 'সব',
        ],

        'select' => [
            'placeholder' => 'সব',
        ],

        'trashed' => [

            'label' => 'রেকর্ড মুছে ফেলুন',

            'only_trashed' => 'শুধু মুছে ফেলা রেকর্ডগুলো',

            'with_trashed' => 'মুছে ফেলা রেকর্ডগুলোর সাথে',

            'without_trashed' => 'মুছে ফেলা রেকর্ডগুলো ছাড়া',

        ],

    ],

    'reorder_indicator' => 'ক্রমানুসারে রেকর্ড টেনে আনুন।',

    'selection_indicator' => [

        'selected_count' => '১টি রেকর্ড নির্বাচিত | :countটি রেকর্ড নির্বাচিত',

        'actions' => [

            'select_all' => [
                'label' => 'সব নির্বাচিত করুন',
            ],

            'deselect_all' => [
                'label' => 'সব অনির্বাচিত করুন',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'ক্রমানুসার',
            ],

            'direction' => [

                'label' => 'ক্রমানুসারের দিক',

                'options' => [
                    'asc' => 'ঊর্ধ্বগামী',
                    'desc' => 'অধোগামী',
                ],

            ],

        ],

    ],

];
