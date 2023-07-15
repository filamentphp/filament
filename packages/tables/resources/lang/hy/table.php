<?php

return [

    'fields' => [

        'search' => [
            'label' => 'Որոնել',
            'placeholder' => 'Որոնել',
        ],

    ],

    'pagination' => [

        'label' => 'Էջավորման նավիգացիա',

        'overview' => 'Ցուցադրվում են :total արդյունքներից :first֊ից :last֊ը',

        'fields' => [

            'records_per_page' => [
                'label' => 'մեկ էջում',
            ],

        ],

        'actions' => [

            'go_to_page' => [
                'label' => 'Գնալ էջ :page',
            ],

            'next' => [
                'label' => 'Հաջորդը',
            ],

            'previous' => [
                'label' => 'Նախորդ',
            ],

        ],

    ],

    'actions' => [

        'filter' => [
            'label' => 'Ֆիլտր',
        ],

        'open_bulk_actions' => [
            'label' => 'Բացել գործողություններ',
        ],

        'toggle_columns' => [
            'label' => 'Փոխարկել սյունակները',
        ],

    ],

    'empty' => [
        'heading' => 'Գրառումներ չեն գտնվել',
    ],

    'filters' => [

        'actions' => [

            'reset' => [
                'label' => 'Վերականգնել ֆիլտրերը',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Բոլորը',
        ],

        'select' => [
            'placeholder' => 'Բոլորը',
        ],

        'trashed' => [

            'label' => 'Ջնջված գրառումները',

            'only_trashed' => 'Միայն ջնջված գրառումները',

            'with_trashed' => 'Ջնջված գրառումներով',

            'without_trashed' => 'Առանց ջնջված գրառումների',

        ],

    ],

    'selection_indicator' => [

        'selected_count' => '1 գրառում ընտրված է։|:count գրառում ընտրված է։',

        'actions' => [

            'select_all' => [
                'label' => 'Ընտրել բոլոր :count֊ը',
            ],

            'deselect_all' => [
                'label' => 'Ապաընտրել բոլորը',
            ],

        ],

    ],

];
