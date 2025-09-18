<?php

return [

    'column_manager' => [

        'heading' => 'Columns',

        'actions' => [

            'apply' => [
                'label' => 'Apply columns',
            ],

            'reset' => [
                'label' => 'Reset',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Action|Actions',
        ],

        'select' => [

            'loading_message' => 'Loading...',

            'no_search_results_message' => 'I search hi a awmlo.',

            'placeholder' => 'I duh thlang rawh...',

            'searching_message' => 'Zawn mek ani...',

            'search_prompt' => 'Zawng turin thil chhu rawh...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Show :count less',
                'expand_list' => 'Show :count more',
            ],

            'more_list_items' => 'and :count more',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Select/deselect all items for bulk actions.',
        ],

        'bulk_select_record' => [
            'label' => 'Select/deselect item :key for bulk actions.',
        ],

        'bulk_select_group' => [
            'label' => 'Select/deselect group :title for bulk actions.',
        ],

        'search' => [
            'label' => 'Zawnna',
            'placeholder' => 'Zawnna',
            'indicator' => 'Zawnna',
        ],

    ],

    'summary' => [

        'heading' => 'Summary',

        'subheadings' => [
            'all' => 'All :label',
            'group' => ':group summary',
            'page' => 'This page',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Average',
            ],

            'count' => [
                'label' => 'Count',
            ],

            'sum' => [
                'label' => 'Sum',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Records awmna thlak a zo',
        ],

        'enable_reordering' => [
            'label' => 'Records awmna thlak',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Group',
        ],

        'open_bulk_actions' => [
            'label' => 'Tamtak tihna',
        ],

        'column_manager' => [
            'label' => 'Column manager',
        ],

    ],

    'empty' => [

        'heading' => ':Model an awm lo',

        'description' => 'A bultan nan :model siam rawh.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Apply filters',
            ],

            'remove' => [
                'label' => 'Remove filter',
            ],

            'remove_all' => [
                'label' => 'Remove all filters',
                'tooltip' => 'Remove all filters',
            ],

            'reset' => [
                'label' => 'Reset',
            ],

        ],

        'heading' => 'Thlit fîmna',

        'indicator' => 'Active filters',

        'multi_select' => [
            'placeholder' => 'All',
        ],

        'select' => [

            'placeholder' => 'All',

            'relationship' => [
                'empty_option_label' => 'None',
            ],

        ],

        'trashed' => [

            'label' => 'Deleted records',

            'only_trashed' => 'Deleted tawh chiah',

            'with_trashed' => 'Deleted tawh telin',

            'without_trashed' => 'Deleted tello in',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Group by',
            ],

            'direction' => [

                'label' => 'Group direction',

                'options' => [
                    'asc' => 'Ascending',
                    'desc' => 'Descending',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Drag and drop the records into order.',

    'selection_indicator' => [

        'selected_count' => 'Record 1 select ani|Records :count select ani',

        'actions' => [

            'select_all' => [
                'label' => 'Avaia :count thlanna',
            ],

            'deselect_all' => [
                'label' => 'Thlan sa paih na',
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

    'default_model_label' => 'record',

];
