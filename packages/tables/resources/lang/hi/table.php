<?php

return [

    'fields' => [

        'search_query' => [
            'label' => 'खोजें',
            'placeholder' => 'खोजें',
        ],

    ],

    'pagination' => [

        'label' => 'पृष्ठ मार्गदर्शन',

        'overview' => ':first से :last प्रविष्टियां :total में से',

        'fields' => [

            'records_per_page' => [
                'label' => 'प्रति पृष्ठ',
            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'पृष्ठ :page पर जाएं',
            ],

            'next' => [
                'label' => 'अगला',
            ],

            'previous' => [
                'label' => 'पिछला',
            ],

        ],

    ],

    'buttons' => [

        'filter' => [
            'label' => 'फ़िल्टर',
        ],

        'open_actions' => [
            'label' => 'क्रियाएँ खोलें',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'क्या आप वाकई ऐसा करना चाहेंगे?',

            'buttons' => [

                'cancel' => [
                    'label' => 'रद्द करें',
                ],

                'confirm' => [
                    'label' => 'पुष्टि करें',
                ],

                'submit' => [
                    'label' => 'सबमिट',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'कोई रिकॉर्ड उपलब्ध नहीं',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'फ़िल्टर रीसेट करें',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'सब',
        ],

        'select' => [
            'placeholder' => 'सब',
        ],

    ],

    'selection_indicator' => [

        'selected_count' => '1 रिकॉर्ड चयनित।|:count रिकॉर्ड चयनित।',

        'buttons' => [

            'select_all' => [
                'label' => 'सभी :count चुने',
            ],

            'deselect_all' => [
                'label' => 'सभी अचयनित करे',
            ],

        ],

    ],

];
