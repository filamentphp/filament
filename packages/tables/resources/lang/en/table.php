<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'and :count more',
        ],

        'messages' => [
            'copied' => 'Copied',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Select/deselect all items for bulk actions.',
        ],

        'bulk_select_record' => [
            'label' => 'Select/deselect item :key for bulk actions.',
        ],

        'search_query' => [
            'label' => 'Search',
            'placeholder' => 'Search',
        ],

    ],

    'pagination' => [

        'label' => 'Pagination Navigation',

        'overview' => '{1} Showing 1 result|[2,*] Showing :first to :last of :total results',

        'fields' => [

            'records_per_page' => [

                'label' => 'per page',

                'options' => [
                    'all' => 'All',
                ],

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

        'disable_reordering' => [
            'label' => 'Finish reordering records',
        ],

        'enable_reordering' => [
            'label' => 'Reorder records',
        ],

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

        'buttons' => [

            'reset_column_searches' => [
                'label' => 'Clear column search',
            ],

        ],

    ],

    'filters' => [

        'buttons' => [

            'remove' => [
                'label' => 'Remove filter',
            ],

            'remove_all' => [
                'label' => 'Remove all filters',
                'tooltip' => 'Remove all filters',
            ],

            'reset' => [
                'label' => 'Reset filters',
            ],

        ],

        'indicator' => 'Active filters',

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

    'reorder_indicator' => 'Drag and drop the records into order.',

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
