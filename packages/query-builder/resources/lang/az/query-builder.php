<?php

return [

    'label' => 'Sorğu qurucusu',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Qruplar',

            'block' => [
                'label' => 'Ayrılma (OR)',
                'or' => 'YA',
            ],

        ],

        'rules' => [

            'label' => 'Qaydalar',

            'item' => [
                'and' => 'VƏ',
            ],

        ],

    ],

    'no_rules' => '(Qayda yoxdur)',

    'item_separators' => [
        'and' => 'VƏ',
        'or' => 'YA',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Doldurulur',
                'inverse' => 'Boşdur',
            ],

            'summary' => [
                'direct' => ':attribute doldurulur',
                'inverse' => ':attribute Boşdur',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Doğrudur',
                    'inverse' => 'Yalandır',
                ],

                'summary' => [
                    'direct' => ':attribute Doğrudur',
                    'inverse' => ':attribute Yalandır',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Sonradır',
                    'inverse' => 'Sonra deyil',
                ],

                'summary' => [
                    'direct' => ':attribute sonradır :date',
                    'inverse' => ':attribute sonra deyil :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Əvvəllərdir',
                    'inverse' => 'Əvvəl deyil',
                ],

                'summary' => [
                    'direct' => ':attribute əvvəllərdir :date',
                    'inverse' => ':attribute əvvəl deyil :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Tarixdir',
                    'inverse' => 'Tarix deyil',
                ],

                'summary' => [
                    'direct' => ':attribute edir :date',
                    'inverse' => ':attribute deyil :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'aydır',
                    'inverse' => 'ay deyil',
                ],

                'summary' => [
                    'direct' => ':attribute edir :month',
                    'inverse' => ':attribute deyil :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'ildir',
                    'inverse' => 'il deyil',
                ],

                'summary' => [
                    'direct' => ':attribute edir :year',
                    'inverse' => ':attribute deyil :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Tarix',
                ],

                'month' => [
                    'label' => 'ay',
                ],

                'year' => [
                    'label' => 'il',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Bərabərdir',
                    'inverse' => 'Bərabər deyil',
                ],

                'summary' => [
                    'direct' => ':attribute bərabərdir :number',
                    'inverse' => ':attribute bərabər deyil :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Maksimumdur',
                    'inverse' => '-dən böyükdür',
                ],

                'summary' => [
                    'direct' => ':attribute maksimumdur :number',
                    'inverse' => ':attribute -dən böyükdür :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Minimumdur',
                    'inverse' => '-dən azdır',
                ],

                'summary' => [
                    'direct' => ':attribute minimumdur :number',
                    'inverse' => ':attribute -dən azdır :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Orta',
                    'summary' => 'Orta :attribute',
                ],

                'max' => [
                    'label' => 'Maks',
                    'summary' => 'Maks :attribute',
                ],

                'min' => [
                    'label' => 'Min',
                    'summary' => 'Min :attribute',
                ],

                'sum' => [
                    'label' => 'məbləğ',
                    'summary' => 'məbləğ of :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Ümumi',
                ],

                'number' => [
                    'label' => 'Nömrə',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Var',
                    'inverse' => 'Yoxdur',
                ],

                'summary' => [
                    'direct' => 'Var :count :relationship',
                    'inverse' => 'Yoxdur :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Maksimum var',
                    'inverse' => '-dən çoxu var',
                ],

                'summary' => [
                    'direct' => 'Maksimum var :count :relationship',
                    'inverse' => '-dən çoxu var :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Minimum var',
                    'inverse' => '-dən azdır',
                ],

                'summary' => [
                    'direct' => 'Minimum var :count :relationship',
                    'inverse' => '-dən azdır :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Boşdur',
                    'inverse' => 'Boş deyil',
                ],

                'summary' => [
                    'direct' => ':relationship boşdur',
                    'inverse' => ':relationship boş deyil',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'edir',
                        'inverse' => 'deyil',
                    ],

                    'multiple' => [
                        'direct' => 'ehtiva edir',
                        'inverse' => 'Tərkibində yoxdur',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship edir :values',
                        'inverse' => ':relationship deyil :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship ehtiva edir :values',
                        'inverse' => ':relationship Tərkibində yoxdur :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' ya ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Dəyər',
                    ],

                    'values' => [
                        'label' => 'Dəyərlər',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'saymaq',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Edir',
                    'inverse' => 'Deyil',
                ],

                'summary' => [
                    'direct' => ':attribute edir :values',
                    'inverse' => ':attribute deyil :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' ya ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Dəyər',
                    ],

                    'values' => [
                        'label' => 'Dəyərlər',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Ehtiva edir',
                    'inverse' => 'Tərkibində yoxdur',
                ],

                'summary' => [
                    'direct' => ':attribute ehtiva edir :text',
                    'inverse' => ':attribute tərkibində yoxdur :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'ilə bitir',
                    'inverse' => 'ilə bitmir',
                ],

                'summary' => [
                    'direct' => ':attribute ilə bitir :text',
                    'inverse' => ':attribute ilə bitmir :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Bərabərdir',
                    'inverse' => 'Bərabər deyil',
                ],

                'summary' => [
                    'direct' => ':attribute bərabərdir :text',
                    'inverse' => ':attribute bərabər deyil :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'ilə başlayır',
                    'inverse' => 'ilə başlamaz',
                ],

                'summary' => [
                    'direct' => ':attribute ilə başlayır :text',
                    'inverse' => ':attribute ilə başlamaz :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Mətn',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Qayda əlavə edin',
        ],

        'add_rule_group' => [
            'label' => 'Qayda qrupu əlavə edin',
        ],

    ],

];
