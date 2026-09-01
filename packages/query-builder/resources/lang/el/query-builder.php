<?php

return [

    'label' => 'Query builder',

    'form' => [

        'operator' => [
            'label' => 'Τελεστής',
        ],

        'or_groups' => [

            'label' => 'Ομάδες',

            'block' => [
                'label' => 'Διαχωρισμός (Ή / OR)',
                'or' => 'Ή (OR)',
            ],

        ],

        'rules' => [

            'label' => 'Κανόνες',

            'item' => [
                'and' => 'ΚΑΙ (AND)',
            ],

        ],

    ],

    'no_rules' => '(Χωρίς κανόνες)',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Is filled',
                'inverse' => 'Is blank',
            ],

            'summary' => [
                'direct' => ':attribute is filled',
                'inverse' => ':attribute is blank',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Is true',
                    'inverse' => 'Is false',
                ],

                'summary' => [
                    'direct' => ':attribute is true',
                    'inverse' => ':attribute is false',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Είναι μετά',
                    'inverse' => 'Δεν είναι μετά',
                ],

                'summary' => [
                    'direct' => ':attribute είναι μετά από τις :date',
                    'inverse' => ':attribute δεν είναι μετά από τις :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Είναι πριν',
                    'inverse' => 'Δεν είναι πριν',
                ],

                'summary' => [
                    'direct' => ':attribute είναι πριν τις :date',
                    'inverse' => ':attribute δεν είναι πριν τις :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Είναι ημέρα',
                    'inverse' => 'Δεν είναι ημέρα',
                ],

                'summary' => [
                    'direct' => ':attribute είναι :date',
                    'inverse' => ':attribute δεν είναι :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Είναι μήνας',
                    'inverse' => 'Δεν είναι μήνας',
                ],

                'summary' => [
                    'direct' => ':attribute είναι :month',
                    'inverse' => ':attribute δεν είναι :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Είναι έτος',
                    'inverse' => 'Δεν είναι έτος',
                ],

                'summary' => [
                    'direct' => ':attribute είναι :year',
                    'inverse' => ':attribute δεν είναι :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Ημέρα',
                ],

                'month' => [
                    'label' => 'Μήνας',
                ],

                'year' => [
                    'label' => 'Έτος',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Ισούται',
                    'inverse' => 'Δεν ισούται',
                ],

                'summary' => [
                    'direct' => ':attribute ισούται :number',
                    'inverse' => ':attribute δεν ισούται :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Is maximum',
                    'inverse' => 'Είναι μεγαλύτερο από',
                ],

                'summary' => [
                    'direct' => ':attribute is maximum :number',
                    'inverse' => ':attribute είναι μεγαλύτερο από :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Is minimum',
                    'inverse' => 'Είναι μικρότερο από',
                ],

                'summary' => [
                    'direct' => ':attribute is minimum :number',
                    'inverse' => ':attribute είναι μικρότερο από :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Μέσος όρος',
                    'summary' => 'Average :attribute',
                ],

                'max' => [
                    'label' => 'Μέγιστο',
                    'summary' => 'Max :attribute',
                ],

                'min' => [
                    'label' => 'Ελάχιστο',
                    'summary' => 'Min :attribute',
                ],

                'sum' => [
                    'label' => 'Άθροισμα',
                    'summary' => 'Sum of :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Aggregate',
                ],

                'number' => [
                    'label' => 'Αριθμός',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Έχει',
                    'inverse' => 'Δεν έχει',
                ],

                'summary' => [
                    'direct' => 'Έχει :count :relationship',
                    'inverse' => 'Δεν έχει :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Has maximum',
                    'inverse' => 'Έχει περισσότερο από',
                ],

                'summary' => [
                    'direct' => 'Has maximum :count :relationship',
                    'inverse' => 'Έχει περισσότερο από :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Has minimum',
                    'inverse' => 'Έχει λιγότερο από',
                ],

                'summary' => [
                    'direct' => 'Has minimum :count :relationship',
                    'inverse' => 'Έχει λιγότερο από :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Είναι κενό',
                    'inverse' => 'Δεν είναι κενό',
                ],

                'summary' => [
                    'direct' => ':relationship είναι κενό/ή',
                    'inverse' => ':relationship δεν είναι  κενό/ή',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Είναι',
                        'inverse' => 'Δεν είναι',
                    ],

                    'multiple' => [
                        'direct' => 'Περιέχει',
                        'inverse' => 'Δεν περιέχει',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship είναι :values',
                        'inverse' => ':relationship δεν είναι :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship περιέχει :values',
                        'inverse' => ':relationship δεν περιέχει :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' or ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Τιμή',
                    ],

                    'values' => [
                        'label' => 'Τιμές',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Σύνολο',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Είναι',
                    'inverse' => 'Δεν είναι',
                ],

                'summary' => [
                    'direct' => ':attribute είναι :values',
                    'inverse' => ':attribute δεν είναι :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' or ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Τιμή',
                    ],

                    'values' => [
                        'label' => 'Τιμές',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Περιέχει',
                    'inverse' => 'Δεν περιέχει',
                ],

                'summary' => [
                    'direct' => ':attribute περιέχει :text',
                    'inverse' => ':attribute δεν πειέχει :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Τελειώνει με',
                    'inverse' => 'Δεν τελειώνει με',
                ],

                'summary' => [
                    'direct' => ':attribute τελειώνει με :text',
                    'inverse' => ':attribute δεν τελειώνει με :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Ισούται',
                    'inverse' => 'Δεν ισούται',
                ],

                'summary' => [
                    'direct' => ':attribute ισούται :text',
                    'inverse' => ':attribute δεν ισούται :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Ξεκινάει με',
                    'inverse' => 'Δεν ξεκινάει με',
                ],

                'summary' => [
                    'direct' => ':attribute ξεκινάει με :text',
                    'inverse' => ':attribute δεν ξεκινάει με :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Κείμενο',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Προσθήκη κανόνα',
        ],

        'add_rule_group' => [
            'label' => 'Add rule group',
        ],

    ],

];
