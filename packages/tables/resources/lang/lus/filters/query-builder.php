<?php

return [

    'label' => 'Query builder',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Groups',

            'block' => [
                'label' => 'Disjunction (OR)',
                'or' => 'OR',
            ],

        ],

        'rules' => [

            'label' => 'Dân',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(Dân a awmlo)',

    'item_separators' => [
        'and' => 'LEH',
        'or' => 'EMAW',
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
                    'direct' => 'Is after',
                    'inverse' => 'Is not after',
                ],

                'summary' => [
                    'direct' => ':attribute is after :date',
                    'inverse' => ':attribute is not after :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Is before',
                    'inverse' => 'Is not before',
                ],

                'summary' => [
                    'direct' => ':attribute is before :date',
                    'inverse' => ':attribute is not before :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Is date',
                    'inverse' => 'Is not date',
                ],

                'summary' => [
                    'direct' => ':attribute is :date',
                    'inverse' => ':attribute is not :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Is month',
                    'inverse' => 'Is not month',
                ],

                'summary' => [
                    'direct' => ':attribute is :month',
                    'inverse' => ':attribute is not :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Is year',
                    'inverse' => 'Is not year',
                ],

                'summary' => [
                    'direct' => ':attribute is :year',
                    'inverse' => ':attribute is not :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Date',
                ],

                'month' => [
                    'label' => 'Month',
                ],

                'year' => [
                    'label' => 'Year',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Intluk',
                    'inverse' => 'Intluklo',
                ],

                'summary' => [
                    'direct' => ':attribute hi :number nen a intluk',
                    'inverse' => ':attribute hi :number nen a intluklo',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Is maximum',
                    'inverse' => 'Is greater than',
                ],

                'summary' => [
                    'direct' => ':attribute is maximum :number',
                    'inverse' => ':attribute is greater than :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Is minimum',
                    'inverse' => 'Is less than',
                ],

                'summary' => [
                    'direct' => ':attribute is minimum :number',
                    'inverse' => ':attribute is less than :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Average',
                    'summary' => 'Average :attribute',
                ],

                'max' => [
                    'label' => 'Max',
                    'summary' => 'Max :attribute',
                ],

                'min' => [
                    'label' => 'Min',
                    'summary' => 'Min :attribute',
                ],

                'sum' => [
                    'label' => 'Sum',
                    'summary' => 'Sum of :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'A vaiin',
                ],

                'number' => [
                    'label' => 'Number',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Has',
                    'inverse' => 'Does not have',
                ],

                'summary' => [
                    'direct' => 'Has :count :relationship',
                    'inverse' => 'Does not have :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Has maximum',
                    'inverse' => 'Has more than',
                ],

                'summary' => [
                    'direct' => 'Has maximum :count :relationship',
                    'inverse' => 'Has more than :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Has minimum',
                    'inverse' => 'Has less than',
                ],

                'summary' => [
                    'direct' => 'Has minimum :count :relationship',
                    'inverse' => 'Has less than :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Is empty',
                    'inverse' => 'Is not empty',
                ],

                'summary' => [
                    'direct' => ':relationship is empty',
                    'inverse' => ':relationship is not empty',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Is',
                        'inverse' => 'Is not',
                    ],

                    'multiple' => [
                        'direct' => 'Contains',
                        'inverse' => 'Does not contain',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship is :values',
                        'inverse' => ':relationship is not :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship contains :values',
                        'inverse' => ':relationship does not contain :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' or ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Value',
                    ],

                    'values' => [
                        'label' => 'Values',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Count',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Is',
                    'inverse' => 'Is not',
                ],

                'summary' => [
                    'direct' => ':attribute is :values',
                    'inverse' => ':attribute is not :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' or ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Value',
                    ],

                    'values' => [
                        'label' => 'Values',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Pai tel',
                    'inverse' => 'Pai tello',
                ],

                'summary' => [
                    'direct' => ':attribute in :text a pai tel',
                    'inverse' => ':attribute in :text a pai tello',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'A tâwpna',
                    'inverse' => 'A tâwplo',
                ],

                'summary' => [
                    'direct' => ':attribute hi :text a tâwp',
                    'inverse' => ':attribute hi :text a tâwplo',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Intluk',
                    'inverse' => 'Intluklo',
                ],

                'summary' => [
                    'direct' => ':attribute hi :text nen a intluk',
                    'inverse' => ':attribute hi :text nen a intluklo',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Inṭan',
                    'inverse' => 'Inṭanlo',
                ],

                'summary' => [
                    'direct' => ':attribute hi :text a inṭan',
                    'inverse' => ':attribute hi :text a inṭanlo',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Thu',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Dân belhna',
        ],

        'add_rule_group' => [
            'label' => 'Dân ho belhna',
        ],

    ],

];
