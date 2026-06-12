<?php

return [

    'label' => 'Հարցման ձևավորիչ',

    'form' => [

        'operator' => [
            'label' => 'Օպերատոր',
        ],

        'or_groups' => [

            'label' => 'Խմբեր',

            'block' => [
                'label' => 'Տարանջատում (կամ)',
                'or' => 'կամ',
            ],

        ],

        'rules' => [

            'label' => 'Կանոններ',

            'item' => [
                'and' => 'և',
            ],

        ],

    ],

    'no_rules' => '(Կանոններ չկան)',

    'item_separators' => [
        'and' => 'և',
        'or' => 'կամ',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Լրացված է',
                'inverse' => 'Դատարկ է',
            ],

            'summary' => [
                'direct' => ':attribute լրացված է',
                'inverse' => ':attribute դատարկ է',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Ճիշտ է',
                    'inverse' => 'Սխալ է',
                ],

                'summary' => [
                    'direct' => ':attribute ճիշտ է',
                    'inverse' => ':attribute սխալ է',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Հետո է',
                    'inverse' => 'Հետո չէ',
                ],

                'summary' => [
                    'direct' => ':attributeը :date-ից հետո է',
                    'inverse' => ':attribute :date-ից հետո չէ ',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Առաջ է',
                    'inverse' => 'Առաջ չէ',
                ],

                'summary' => [
                    'direct' => ':attribute :date-ից առաջ է',
                    'inverse' => ':attribute :date-ից առաջ է',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Ամսաթիվ է',
                    'inverse' => 'Ամսաթիվ չէ',
                ],

                'summary' => [
                    'direct' => ':attributeը :date է',
                    'inverse' => ':attributeը :date չէ',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Ամիս է',
                    'inverse' => 'Ամիս չէ',
                ],

                'summary' => [
                    'direct' => ':attributeը :month է',
                    'inverse' => ':attributeը :month չէ',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Տարի է',
                    'inverse' => 'Տարի չէ',
                ],

                'summary' => [
                    'direct' => ':attributeը :year է',
                    'inverse' => ':attributeը :year չէ',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Օր',
                ],

                'month' => [
                    'label' => 'Ամիս',
                ],

                'year' => [
                    'label' => 'Տարի',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Հավասար է',
                    'inverse' => 'Հավասար չէ',
                ],

                'summary' => [
                    'direct' => ':attributeը հավասար է :number',
                    'inverse' => ':attributeը հավասար չէ :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Առավելագույնն է',
                    'inverse' => 'Ավելին քան',
                ],

                'summary' => [
                    'direct' => ':attribute առավելագույնն է :number',
                    'inverse' => ':attribute ավելին քան :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Նվազագույն է',
                    'inverse' => 'Ավելի քիչ, քան',
                ],

                'summary' => [
                    'direct' => ':attribute նվազագույն է :number',
                    'inverse' => ':attribute ավելի քիչ, քան :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Միջին',
                    'summary' => 'Միջին :attribute',
                ],

                'max' => [
                    'label' => 'Առավելագույն',
                    'summary' => 'Առավելագույն :attribute',
                ],

                'min' => [
                    'label' => 'Նվազագույն',
                    'summary' => 'Նվազագույն :attribute',
                ],

                'sum' => [
                    'label' => 'Գումար',
                    'summary' => 'Գումարը :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Կուտակային',
                ],

                'number' => [
                    'label' => 'Թիվ',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Ունի',
                    'inverse' => 'Չունի',
                ],

                'summary' => [
                    'direct' => 'Ունի :count :relationship',
                    'inverse' => 'Չունի :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Ունի առավելագույնը',
                    'inverse' => 'Ունի առավել քան',
                ],

                'summary' => [
                    'direct' => 'Ունի առավելագույնը :count :relationship',
                    'inverse' => 'Ունի առավել քան :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Ունի նվազագույնը',
                    'inverse' => 'Ունի պակաս քան',
                ],

                'summary' => [
                    'direct' => 'Ունի նվազագույնը :count :relationship',
                    'inverse' => 'Ունի պակաս քան :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Դատարկ է',
                    'inverse' => 'Դատարկ չէ',
                ],

                'summary' => [
                    'direct' => ':relationshipը դատարկ է',
                    'inverse' => ':relationshipը դատարկ չէ',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Արդյոք',
                        'inverse' => 'Ոչ',
                    ],

                    'multiple' => [
                        'direct' => 'Պարունակում է',
                        'inverse' => 'Չի պարունակում',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationshipը :values է',
                        'inverse' => ':relationshipը :values չէ',
                    ],

                    'multiple' => [
                        'direct' => ':relationship պարունակում է :values',
                        'inverse' => ':relationship չի պարունակում :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' կամ ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Արժեք',
                    ],

                    'values' => [
                        'label' => 'Արժեքներ',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Քանակ',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Արդյոք',
                    'inverse' => 'Ոչ',
                ],

                'summary' => [
                    'direct' => ':attributeը :values է',
                    'inverse' => ':attributeը :values չէ',
                    'values_glue' => [
                        ', ',
                        'final' => ' կամ ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Արժեք',
                    ],

                    'values' => [
                        'label' => 'Արժեքներ',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Պարունակում է',
                    'inverse' => 'Չի պարունակում',
                ],

                'summary' => [
                    'direct' => ':attributeը պարունակում է :text',
                    'inverse' => ':attributeը չի պարունակում :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Վերջանում է',
                    'inverse' => 'Չի վերջանում',
                ],

                'summary' => [
                    'direct' => ':attributeը վերջանում է :text-ով',
                    'inverse' => ':attributeը չի վերջանում :text-ով',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Հավասար է',
                    'inverse' => 'Հավասար չէ',
                ],

                'summary' => [
                    'direct' => ':attributeը հավասար է :text-ով',
                    'inverse' => ':attributeը հավասար չէ :text-ով',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Սկսում է',
                    'inverse' => 'Չի սկսում',
                ],

                'summary' => [
                    'direct' => ':attribute սկսում է :text-ով',
                    'inverse' => ':attribute չի սկսում :text-ով',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Տեքստ',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Ավելացնել կանոն',
        ],

        'add_rule_group' => [
            'label' => 'Ավելացնել կանոնի խումբ',
        ],

    ],

];
