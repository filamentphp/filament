<?php

return [
    'column_manager' => [
        'heading' => 'Mga Column',
        'actions' => [
            'apply' => [
                'label' => 'I-apply ang mga column',
            ],
            'reorder' => [
                'label' => 'Ayusin muli ang column',
            ],
            'reset' => [
                'label' => 'I-reset',
            ],
        ],
    ],
    'columns' => [
        'actions' => [
            'label' => 'Aksyon|Mga Aksyon',
        ],
        'icon' => [
            'boolean' => [
                'true' => 'Oo',
                'false' => 'Hindi',
            ],
        ],
        'select' => [
            'loading_message' => 'Naglo-load...',
            'no_options_message' => 'Walang available na opsyon.',
            'no_search_results_message' => 'Walang opsyong tumutugma sa iyong hinahanap.',
            'placeholder' => 'Pumili ng opsyon',
            'searching_message' => 'Naghahanap...',
            'search_prompt' => 'Magsimulang mag-type para maghanap...',
        ],
        'text' => [
            'actions' => [
                'collapse_list' => 'Magpakita ng mas kaunting :count',
                'expand_list' => 'Magpakita ng :count pa',
            ],
            'more_list_items' => 'at :count pa',
        ],
    ],
    'fields' => [
        'bulk_select_page' => [
            'label' => 'Piliin/alisin sa pagkakapili ang lahat ng item para sa bulk actions.',
        ],
        'bulk_select_record' => [
            'label' => 'Piliin/alisin sa pagkakapili ang item na :key para sa bulk actions.',
        ],
        'bulk_select_group' => [
            'label' => 'Piliin/alisin sa pagkakapili ang grupong :title para sa bulk actions.',
        ],
        'search' => [
            'label' => 'Maghanap',
            'placeholder' => 'Maghanap',
            'indicator' => 'Maghanap',
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
            'average' => [
                'label' => 'Katamtaman',
            ],
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
            'label' => 'Tapusin ang muling pag-aayos ng mga rekord',
        ],
        'enable_reordering' => [
            'label' => 'Ayusin muli ang mga rekord',
        ],
        'reorder_record' => [
            'label' => 'Ayusin muli ang item na :key',
        ],
        'filter' => [
            'label' => 'I-filter',
        ],
        'group' => [
            'label' => 'Grupo',
        ],
        'open_bulk_actions' => [
            'label' => 'Mga bulk action',
        ],
        'column_manager' => [
            'label' => 'Tagapamahala ng column',
        ],
        'toggle_record_content' => [
            'label' => 'I-expand/i-collapse ang item na :key',
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
                'label' => 'Alisin lahat ng filter',
                'tooltip' => 'Alisin lahat ng filter',
            ],
            'reset' => [
                'label' => 'I-reset',
            ],
        ],
        'heading' => 'Mga Filter',
        'indicator' => 'Mga aktibong filter',
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
            'label' => 'Mga na-delete na rekord',
            'only_trashed' => 'Mga na-delete na rekord lang',
            'with_trashed' => 'Kasama ang mga na-delete na rekord',
            'without_trashed' => 'Hindi kasama ang mga na-delete na rekord',
        ],
    ],
    'grouping' => [
        'fields' => [
            'group' => [
                'label' => 'I-grupo ayon sa',
            ],
            'direction' => [
                'label' => 'Direksyon ng paggrupo',
                'options' => [
                    'asc' => 'Pataas',
                    'desc' => 'Pababa',
                ],
            ],
        ],
    ],
    'loading' => 'Naglo-load...',
    'reorder_indicator' => 'I-drag at i-drop ang mga rekord sa tamang ayos.',
    'result_count' => '{0} Walang resulta|{1} :count resulta|[2,*] :count resulta',
    'selection_indicator' => [
        'selected_count' => '1 rekord ang napili|:count rekord ang napili',
        'actions' => [
            'select_all' => [
                'label' => 'Piliin lahat ng :count',
            ],
            'deselect_all' => [
                'label' => 'Alisin sa pagkakapili ang lahat',
            ],
        ],
    ],
    'sorting' => [
        'fields' => [
            'column' => [
                'label' => 'I-sort ayon sa',
            ],
            'direction' => [
                'label' => 'Direksyon ng pag-sort',
                'options' => [
                    'asc' => 'Pataas',
                    'desc' => 'Pababa',
                ],
            ],
        ],
    ],
    'default_model_label' => 'rekord',
];
