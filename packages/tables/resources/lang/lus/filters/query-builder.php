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

            'label' => 'Rules',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(No rules)',

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
                    'direct' => 'Equals',
                    'inverse' => 'Does not equal',
                ],

                'summary' => [
                    'direct' => ':attribute equals :number',
                    'inverse' => ':attribute does not equal :number',
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
                    'label' => 'Aggregate',
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
                    'direct' => 'Contains',
                    'inverse' => 'Does not contain',
                ],

                'summary' => [
                    'direct' => ':attribute contains :text',
                    'inverse' => ':attribute does not contain :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Ends with',
                    'inverse' => 'Does not end with',
                ],

                'summary' => [
                    'direct' => ':attribute ends with :text',
                    'inverse' => ':attribute does not end with :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Equals',
                    'inverse' => 'Does not equal',
                ],

                'summary' => [
                    'direct' => ':attribute equals :text',
                    'inverse' => ':attribute does not equal :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Starts with',
                    'inverse' => 'Does not start with',
                ],

                'summary' => [
                    'direct' => ':attribute starts with :text',
                    'inverse' => ':attribute does not start with :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Text',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Add rule',
        ],

        'add_rule_group' => [
            'label' => 'Add rule group',
        ],

    ],

];
