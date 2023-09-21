<?php

return [

    'column_toggle' => [

        'heading' => 'الأعمدة',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'و :count إضافية',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'تحديد / إلغاء تحديد كافة العناصر للإجراءات الجماعية.',
        ],

        'bulk_select_record' => [
            'label' => 'تحديد / إلغاء تحديد العنصر :key للإجراءات الجماعية.',
        ],

        'bulk_select_group' => [
            'label' => 'تحديد / إلغاء تحديد المجموعة :title للإجراءات الجماعية.',
        ],

        'search' => [
            'label' => 'بحث',
            'placeholder' => 'بحث',
            'indicator' => 'بحث',
        ],

    ],

    'summary' => [

        'heading' => 'الملخص',

        'subheadings' => [
            'all' => 'كافة :label',
            'group' => 'ملخص :group',
            'page' => 'هذه الصفحة',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'المتوسط',
            ],

            'count' => [
                'label' => 'العدد',
            ],

            'sum' => [
                'label' => 'المجموع',
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

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Group',
        ],

        'open_bulk_actions' => [
            'label' => 'Bulk actions',
        ],

        'toggle_columns' => [
            'label' => 'Toggle columns',
        ],

    ],

    'empty' => [

        'heading' => 'No :model',

        'description' => 'Create a :model to get started.',

    ],

    'filters' => [

        'actions' => [

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
                'placeholder' => 'Group by',
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

];
