<?php

return [

    'column_manager' => [

        'heading' => 'სვეტები',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'აჩვენე :count ნაკლები',
                'expand_list' => 'აჩვენე :count მეტი',
            ],

            'more_list_items' => 'და კიდევ :count',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'მონიშნეთ/გააუქმეთ ყველა ელემენტი საერთო ქმედებებისთვის.',
        ],

        'bulk_select_record' => [
            'label' => 'მონიშნეთ/გააუქმეთ ელემენტი :key საერთო ქმედებებისთვის.',
        ],

        'bulk_select_group' => [
            'label' => 'მონიშნეთ/გააუქმეთ ჯგუფი :title საერთო ქმედებებისთვის.',
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
            'label' => 'ჩანაწერების გადალაგების დასრულება',
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

        'column_manager' => [
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
                'label' => 'ფილტრის მოხსნა',
            ],

            'remove_all' => [
                'label' => 'ყველა ფილტრის მოხსნა',
                'tooltip' => 'ყველა ფილტრის მოხსნა',
            ],

            'reset' => [
                'label' => 'გასუფთავება',
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
                'label' => 'დაჯგუფებულია',
                'placeholder' => 'დაჯგუფებულია',
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
                'label' => 'სორტირება',
            ],

            'direction' => [

                'label' => 'სორტირება',

                'options' => [
                    'asc' => 'ზრდადობით',
                    'desc' => 'კლებადობით',
                ],

            ],

        ],

    ],

];
