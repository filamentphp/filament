<?php

return [

    'column_toggle' => [

        'heading' => 'स्तम्भहरू',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'र थप :count',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'बल्क कार्यका लागि सबै वस्तुहरू चयन/अचयन गर्नुहोस्।',
        ],

        'bulk_select_record' => [
            // वस्तु चयन/अचयन गर्नुहोस् : बल्क कार्यहरूको लागि कुञ्जी।
            'label' => ':key वस्तु चयन/अचयन गर्नुहोस् : बल्क कार्यहरूको लागि।',
        ],

        'search' => [
            'label' => 'खोज',
            'placeholder' => 'खोज',
            'indicator' => 'खोज',
        ],

    ],

    'summary' => [

        'heading' => 'सारांश',

        'subheadings' => [
            'all' => 'सबै :label',
            'group' => ':group को सारांश',
            'page' => 'यो पृष्ठ',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'औसत',
            ],

            'count' => [
                'label' => 'गणना',
            ],

            'sum' => [
                'label' => 'योगफल',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'रेकर्ड पुन क्रमबद्ध समाप्त गर्नुहोस्',
        ],

        'enable_reordering' => [
            'label' => 'रेकर्डहरू पुन क्रमबद्ध गर्नुहोस्',
        ],

        'filter' => [
            'label' => 'फिल्टर गर्नुहोस्',
        ],

        'group' => [
            'label' => 'समूहबद्ध गर्नुहोस्',
        ],

        'open_bulk_actions' => [
            'label' => 'बल्क कार्यहरू',
        ],

        'toggle_columns' => [
            'label' => 'स्तम्भहरू टगल गर्नुहोस्',
        ],

    ],

    'empty' => [

        'heading' => ':model छैन',

        'description' => 'सुरु गर्न :model सिर्जना गर्नुहोस्।',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'फिल्टर हटाउनुहोस्',
            ],

            'remove_all' => [
                'label' => 'सबै फिल्टरहरू हटाउनुहोस्',
                'tooltip' => 'सबै फिल्टरहरू हटाउनुहोस्',
            ],

            'reset' => [
                'label' => 'रिसेट गर्नुहोस्',
            ],

        ],

        'heading' => 'फिल्टरहरू',

        'indicator' => 'सक्रिय फिल्टरहरू',

        'multi_select' => [
            'placeholder' => 'सबै',
        ],

        'select' => [
            'placeholder' => 'सबै',
        ],

        'trashed' => [

            'label' => 'मेटिएका रेकर्डहरू',

            'only_trashed' => 'केवल मेटाइएको रेकर्डहरू',

            'with_trashed' => 'मेटाइएका रेकर्डहरूसँग',

            'without_trashed' => 'मेटाइएको रेकर्ड बिना',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'समायोजन गर्नुहोस्',
                'placeholder' => 'समायोजन गर्नुहोस्',
            ],

            'direction' => [

                'label' => 'समूहको दिशा',

                'options' => [
                    'asc' => 'आरोहण',
                    'desc' => 'अवतरण',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'क्रम मा रेकर्ड तान्नुहोस् र छोड्नुहोस्।',

    'selection_indicator' => [

        'selected_count' => 'एउटा रेकर्ड चयन गरियो|:count वटा रेकर्ड चयन गरियो',

        'actions' => [

            'select_all' => [
                'label' => ':count वटा चयन गर्नुहोस्',
            ],

            'deselect_all' => [
                'label' => 'सबै अचयन गर्नुहोस्',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'क्रमबद्ध गर्नुहोस्',
            ],

            'direction' => [

                'label' => 'क्रमबद्ध दिशा',

                'options' => [
                    'asc' => 'आरोहण',
                    'desc' => 'अवतरण',
                ],

            ],

        ],

    ],

];
