<?php

return [

    'column_manager' => [

        'heading' => 'Columns',

        'actions' => [

            'apply' => [
                'label' => 'Apply columns',
            ],

            'reset' => [
                'label' => 'Tihṭhatna',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Thiltihna|Thiltihnate',
        ],

        'select' => [

            'loading_message' => 'Loading...',

            'no_options_message' => 'Duh thlan tur a awmlo.',

            'no_search_results_message' => 'I thilzawn hi a awmlo.',

            'placeholder' => 'I duh thlang rawh...',

            'searching_message' => 'Zawn mek ani...',

            'search_prompt' => 'Zawng turin thil chhu rawh...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => ':count in tilang tlem rawh',
                'expand_list' => ':count in tilang tam rawh',
            ],

            'more_list_items' => 'leh adang :count',

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

        'heading' => 'Khai khâwmna',

        'subheadings' => [
            'all' => ':Label a vaiin',
            'group' => ':group khai khâwm',
            'page' => 'Hemi phêkah',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Chawhrualin',
            ],

            'count' => [
                'label' => 'Count',
            ],

            'sum' => [
                'label' => 'Belh khâwm',
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
            'label' => 'Thlit fîmna',
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

        'heading' => ':Model a awm lo',

        'description' => 'A bulṭan nan :model siam rawh.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Thli fîm rawh',
            ],

            'remove' => [
                'label' => 'Thlit fîm hlihna',
            ],

            'remove_all' => [
                'label' => 'Thlit fîm ho hlihna',
                'tooltip' => 'Thlit fîm ho hlihna',
            ],

            'reset' => [
                'label' => 'Tihṭhatna',
            ],

        ],

        'heading' => 'Thlit fîmna',

        'indicator' => 'Thlit fîm mek',

        'multi_select' => [
            'placeholder' => 'A vaiin',
        ],

        'select' => [

            'placeholder' => 'A vaiin',

            'relationship' => [
                'empty_option_label' => 'Awmlo',
            ],

        ],

        'trashed' => [

            'label' => 'Thai bo chhinchhiahna',

            'only_trashed' => 'Thai bo tawh chiah',

            'with_trashed' => 'Thai bo tawh telin',

            'without_trashed' => 'Thai bo tello in',

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
                    'asc' => 'Hmasa',
                    'desc' => 'Hnuhnung',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'A indawt dânin record dah kual rawh.',

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
                'label' => 'Thliarna',
            ],

            'direction' => [

                'label' => 'Thliarna lam',

                'options' => [
                    'asc' => 'Hmasa',
                    'desc' => 'Hnuhnung',
                ],

            ],

        ],

    ],

    'default_model_label' => 'record',

];
