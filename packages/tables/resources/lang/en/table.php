<?php

return [

    'column_manager' => [

        'heading' => 'Columns',

        'position' => 'Position :position of :count.',

        'position_in_group' => 'Position :position of :count in :group.',

        'reordered' => ':column moved to position :position of :count.',

        'reordered_deferred' => ':column moved to position :position of :count. Apply columns to save your changes.',

        'reordered_in_group' => ':column moved to position :position of :count in :group.',

        'reordered_in_group_deferred' => ':column moved to position :position of :count in :group. Apply columns to save your changes.',

        'actions' => [

            'apply' => [
                'label' => 'Apply columns',
            ],

            'move_up' => [
                'label' => 'Move :column up',
            ],

            'move_down' => [
                'label' => 'Move :column down',
            ],

            'reorder' => [
                'label' => 'Reorder column',
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

        'editable' => [
            'saving' => 'Saving change...',
            'saved' => 'Change saved.',
            'failure' => 'The save could not be confirmed. Refresh the page before trying again.',
            'unavailable' => 'This item can no longer be edited.',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'Yes',
                'false' => 'No',
            ],

        ],

        'select' => [

            'loading_message' => 'Loading...',

            'no_options_message' => 'No options available.',

            'no_search_results_message' => 'No options match your search.',

            'placeholder' => 'Select an option',

            'searching_message' => 'Searching...',

            'search_prompt' => 'Start typing to search',

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
            'label' => 'Search',
            'placeholder' => 'Search',
            'indicator' => 'Search',
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
            'label' => 'Finish reordering records',
        ],

        'enable_reordering' => [
            'label' => 'Reorder records',
        ],

        'reorder_record' => [
            'label' => 'Reorder item :key',
        ],

        'move_record_up' => [
            'label' => 'Move :label up',
        ],

        'move_record_down' => [
            'label' => 'Move :label down',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Group',
        ],

        'open_bulk_actions' => [
            'label' => 'Bulk actions',
        ],

        'column_manager' => [
            'label' => 'Column manager',
        ],

        'toggle_record_content' => [
            'label' => 'Expand/collapse item :key',
        ],

    ],

    'empty' => [

        'heading' => 'No :model',

        'description' => 'Create a :model to get started.',

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

        'heading' => 'Filters',

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

            'only_trashed' => 'Only deleted records',

            'with_trashed' => 'With deleted records',

            'without_trashed' => 'Without deleted records',

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

    'loading' => 'Loading...',

    'reorder_indicator' => 'Drag and drop the records into order.',

    'reordering' => [
        'position' => ':label, position :position of :count.',
        'moved' => ':label moved to position :position of :count.',
    ],

    'result_count' => '{0} No results|{1} :count result|[2,*] :count results',

    'selection_indicator' => [

        'selected_count' => '1 record selected|:count records selected',

        'actions' => [

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

    'default_model_label' => 'record',

];
