<?php

return [

    'column_toggle' => [

        'heading' => 'სვეტები',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'აჩვენეთ :count ნაკლები',
                'expand_list' => 'აჩვენეთ :count მეტი',
            ],

            'more_list_items' => 'და კიდევ :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'მონიშნეთ/გაუქმეთ ყველა ელემენტი საერთო ქმედებებისთვის.',
        ],

        'bulk_select_record' => [
            'label' => 'მონიშნეთ/გაუქმეთ ელემენტი :key საერთო ქმედებებისთვის.',
        ],

        'bulk_select_group' => [
            'label' => 'მონიშნეთ/გაუქმეთ ჯგუფი :title საერთო ქმედებებისთვის.',
        ],

        'search' => [
            'label' => 'ძებნა',
            'placeholder' => 'ძებნა',
            'indicator' => 'ძებნა',
        ],

    ],

    'summary' => [

        'heading' => 'შეჯამება',

        'subheadings' => [
            'all' => 'ყველა :label',
            'group' => ':group შეჯამება',
            'page' => 'ეს გვერდი',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'საშუალო',
            ],

            'count' => [
                'label' => 'რაოდენობა',
            ],

            'sum' => [
                'label' => 'ჯამი',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'დასრულება ჩანაწერების გადალაგების',
        ],

        'enable_reordering' => [
            'label' => 'ჩანაწერების გადალაგება',
        ],

        'filter' => [
            'label' => 'ფილტრი',
        ],

        'group' => [
            'label' => 'ჯგუფი',
        ],

        'open_bulk_actions' => [
            'label' => 'საერთო ქმედებები',
        ],

        'toggle_columns' => [
            'label' => 'სვეტების გადართვა',
        ],

    ],

    'empty' => [

        'heading' => 'არაა :model',

        'description' => 'შექმენით :model რომ დაიწყოთ.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'ფილტრების გამოყენება',
            ],

            'remove' => [
                'label' => 'ფილტრის ამოღება',
            ],

            'remove_all' => [
                'label' => 'ყველა ფილტრის ამოღება',
                'tooltip' => 'ყველა ფილტრის ამოღება',
            ],

            'reset' => [
                'label' => 'გადატვირთვა',
            ],

        ],

        'heading' => 'ფილტრები',

        'indicator' => 'აქტიური ფილტრები',

        'multi_select' => [
            'placeholder' => 'ყველა',
        ],

        'select' => [
            'placeholder' => 'ყველა',
        ],

        'trashed' => [

            'label' => 'წაშლილი ჩანაწერები',

            'only_trashed' => 'მხოლოდ წაშლილი ჩანაწერები',

            'with_trashed' => 'წაშლილ ჩანაწერებთან ერთად',

            'without_trashed' => 'წაშლილი ჩანაწერების გარეშე',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'ჯგუფის მიხედვით',
                'placeholder' => 'ჯგუფის მიხედვით',
            ],

            'direction' => [

                'label' => 'ჯგუფის მიმართულება',

                'options' => [
                    'asc' => 'ზრდადი',
                    'desc' => 'კლებადი',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'ჩანაწერების გადათრევა და ჩამოშვება რიგში.',

    'selection_indicator' => [

        'selected_count' => '1 ჩანაწერი მონიშნულია|:count ჩანაწერები მონიშნულია',

        'actions' => [

            'select_all' => [
                'label' => 'მონიშნეთ ყველა :count',
            ],

            'deselect_all' => [
                'label' => 'ყველას მონიშვნის გაუქმება',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'დალაგების მიხედვით',
            ],

            'direction' => [

                'label' => 'დალაგების მიმართულება',

                'options' => [
                    'asc' => 'ზრდადი',
                    'desc' => 'კლებადი',
                ],

            ],

        ],

    ],

];
