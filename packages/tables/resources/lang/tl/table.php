<?php

return [

    'column_manager' => [

        'heading' => 'Mga column',

        'actions' => [

            'apply' => [
                'label' => 'I-apply ang mga column',
            ],

            'reorder' => [
                'label' => 'Ayusin ang pagkakasunod ng column',
            ],

            'reset' => [
                'label' => 'I-reset',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Action|Mga action',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'Oo',
                'false' => 'Hindi',
            ],

        ],

        'select' => [

            'loading_message' => 'Naglo-load...',

            'no_options_message' => 'Walang available na option.',

            'no_search_results_message' => 'Walang option na tumutugma sa paghahanap mo.',

            'placeholder' => 'Pumili ng option',

            'searching_message' => 'Naghahanap...',

            'search_prompt' => 'Magsimulang mag-type para maghanap...',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Magpakita ng :count mas kaunti',
                'expand_list' => 'Magpakita ng :count pa',
            ],

            'more_list_items' => 'at :count pa',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Piliin/alisin ang pili sa lahat ng item para sa bulk actions.',
        ],

        'bulk_select_record' => [
            'label' => 'Piliin/alisin ang pili sa item :key para sa bulk actions.',
        ],

        'bulk_select_group' => [
            'label' => 'Piliin/alisin ang pili sa group :title para sa bulk actions.',
        ],

        'search' => [
            'label' => 'Maghanap',
            'placeholder' => 'Maghanap',
            'indicator' => 'Paghahanap',
        ],

    ],

    'summary' => [

        'heading' => 'Buod',

        'subheadings' => [
            'all' => 'Lahat ng :label',
            'group' => 'Buod ng :group',
            'page' => 'Pahinang ito',
        ],

        'summarizers' => [

            'count' => [
                'label' => 'Bilang',
            ],

            'sum' => [
                'label' => 'Kabuuan',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Tapusin ang pag-aayos ng mga record',
        ],

        'enable_reordering' => [
            'label' => 'Ayusin ang pagkakasunod ng mga record',
        ],

        'reorder_record' => [
            'label' => 'Ayusin ang item :key',
        ],

        'filter' => [
            'label' => 'I-filter',
        ],

        'group' => [
            'label' => 'I-group',
        ],

        'toggle_record_content' => [
            'label' => 'I-expand/i-collapse ang item :key',
        ],

    ],

    'empty' => [

        'heading' => 'Walang :model',

        'description' => 'Gumawa ng :model para makapagsimula.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'I-apply ang mga filter',
            ],

            'remove' => [
                'label' => 'Alisin ang filter',
            ],

            'remove_all' => [
                'label' => 'Alisin ang lahat ng filter',
                'tooltip' => 'Alisin ang lahat ng filter',
            ],

            'reset' => [
                'label' => 'I-reset',
            ],

        ],

        'heading' => 'Mga filter',

        'indicator' => 'Mga active na filter',

        'multi_select' => [
            'placeholder' => 'Lahat',
        ],

        'select' => [

            'placeholder' => 'Lahat',

            'relationship' => [
                'empty_option_label' => 'Wala',
            ],

        ],

        'trashed' => [

            'label' => 'Mga naburang record',

            'only_trashed' => 'Mga naburang record lang',

            'with_trashed' => 'Kasama ang mga naburang record',

            'without_trashed' => 'Wala ang mga naburang record',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'I-group ayon sa',
            ],

            'direction' => [

                'label' => 'Direksyon ng group',

                'options' => [
                    'asc' => 'Paakyat',
                    'desc' => 'Pababa',
                ],

            ],

        ],

    ],

    'loading' => 'Naglo-load...',

    'reorder_indicator' => 'I-drag at i-drop ang mga record sa tamang pagkakasunod.',

    'result_count' => '{0} Walang resulta|{1} :count resulta|[2,*] :count resulta',

    'selection_indicator' => [

        'selected_count' => '1 record ang napili|:count record ang napili',

        'actions' => [

            'select_all' => [
                'label' => 'Piliin lahat ng :count',
            ],

            'deselect_all' => [
                'label' => 'Alisin ang lahat ng pili',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'I-sort ayon sa',
            ],

            'direction' => [

                'label' => 'Direksyon ng sort',

                'options' => [
                    'asc' => 'Paakyat',
                    'desc' => 'Pababa',
                ],

            ],

        ],

    ],

];
