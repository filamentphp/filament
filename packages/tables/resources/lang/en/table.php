<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'and :count more',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Search',
            'placeholder' => 'Search',
        ],

    ],

    'pagination' => [

        'label' => 'Pagination Navigation',

        'overview' => 'Showing :first to :last of :total results',

        'fields' => [

            'records_per_page' => [
                'label' => 'per page',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Go to page :page',
            ],

            'next' => [
                'label' => 'Next',
            ],

            'previous' => [
                'label' => 'Previous',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'Filter',
        ],

        'open_actions' => [
            'label' => 'Open actions',
        ],

        'toggle_columns' => [
            'label' => 'Toggle columns',
        ],

    ],

    'empty' => [
        'heading' => 'No records found',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Reset filters',
            ],

            'close' => [
                'label' => 'Close',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'All',
        ],

        'select' => [
            'placeholder' => 'All',
        ],

        'trashed' => [

            'label' => 'Deleted records',

            'only_trashed' => 'Only deleted records',

            'with_trashed' => 'With deleted records',

            'without_trashed' => 'Without deleted records',

        ],

    ],

    'selection_indicator' => [

        'selected_count' => '1 record selected.|:count records selected.',

        'buttons' => [

            'select_all' => [
                'label' => 'Select all :count',
            ],

            'deselect_all' => [
                'label' => 'Deselect all',
            ],

        ],

    ],

];
