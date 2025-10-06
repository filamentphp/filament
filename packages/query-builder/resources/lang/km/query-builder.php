<?php

return [

    'label' => 'Query builder',

    'form' => [

        'operator' => [
            'label' => 'ប្រតិបត្តិករ',
        ],

        'or_groups' => [

            'label' => 'ក្រុម',

            'block' => [
                'label' => 'ការផ្តាច់ខ្លួន (ឬ)',
                'or' => 'ឬ',
            ],

        ],

        'rules' => [

            'label' => 'ច្បាប់',

            'item' => [
                'and' => 'និង',
            ],

        ],

    ],

    'no_rules' => '(មិន​មាន​ច្បាប់)',

    'item_separators' => [
        'and' => 'និង',
        'or' => 'ឬ',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'ត្រូវបានបំពេញ',
                'inverse' => 'គឺទទេ',
            ],

            'summary' => [
                'direct' => ':attribute ត្រូវបានបំពេញ',
                'inverse' => ':attribute គឺទទេ',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'គឺពិត',
                    'inverse' => 'មិនពិត',
                ],

                'summary' => [
                    'direct' => ':attribute គឺពិត',
                    'inverse' => ':attribute មិនពិត',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'គឺបន្ទាប់ពី',
                    'inverse' => 'គឺមិនមែនបន្ទាប់ពី',
                ],

                'summary' => [
                    'direct' => ':attribute គឺបន្ទាប់ពី :date',
                    'inverse' => ':attribute គឺមិនមែនបន្ទាប់ពី :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'គឺពីមុន',
                    'inverse' => 'មិនមែនពីមុនទេ',
                ],

                'summary' => [
                    'direct' => ':attribute គឺពីមុន :date',
                    'inverse' => ':attribute មិនមែនពីមុនទេ :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'គឺកាលបរិច្ឆេទ',
                    'inverse' => 'មិនមែនជាកាលបរិច្ឆេទទេ',
                ],

                'summary' => [
                    'direct' => ':attribute គឺ :date',
                    'inverse' => ':attribute មិនមែន :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'គឺខែ',
                    'inverse' => 'មិនមែនខែទេ',
                ],

                'summary' => [
                    'direct' => ':attribute គឺ :month',
                    'inverse' => ':attribute មិនមែន :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'គឺជាឆ្នាំ',
                    'inverse' => 'មិនមែនឆ្នាំទេ',
                ],

                'summary' => [
                    'direct' => ':attribute គឺ :year',
                    'inverse' => ':attribute មិនមែន :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'កាលបរិច្ឆេទ',
                ],

                'month' => [
                    'label' => 'ខែ',
                ],

                'year' => [
                    'label' => 'ឆ្នាំ',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'ស្មើ',
                    'inverse' => 'មិនស្មើគ្នា',
                ],

                'summary' => [
                    'direct' => ':attribute ស្មើ :number',
                    'inverse' => ':attribute មិនស្មើគ្នា :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'គឺអតិបរមា',
                    'inverse' => 'គឺធំជាង',
                ],

                'summary' => [
                    'direct' => ':attribute គឺអតិបរមា :number',
                    'inverse' => ':attribute គឺធំជាង :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'គឺអប្បបរមា',
                    'inverse' => 'គឺតិចជាង',
                ],

                'summary' => [
                    'direct' => ':attribute គឺអប្បបរមា :number',
                    'inverse' => ':attribute គឺតិចជាង :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'មធ្យម',
                    'summary' => 'មធ្យម :attribute',
                ],

                'max' => [
                    'label' => 'អតិបរមា',
                    'summary' => 'អតិបរមា :attribute',
                ],

                'min' => [
                    'label' => 'អប្បបរមា',
                    'summary' => 'អប្បបរមា :attribute',
                ],

                'sum' => [
                    'label' => 'បូក',
                    'summary' => 'ផលបូកនៃ :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'សរុប',
                ],

                'number' => [
                    'label' => 'ចំនួន',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => '​មាន',
                    'inverse' => 'មិន​មាន',
                ],

                'summary' => [
                    'direct' => '​មាន :count :relationship',
                    'inverse' => 'មិន​មាន :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'មានអតិបរមា',
                    'inverse' => 'មានច្រើនជាង',
                ],

                'summary' => [
                    'direct' => 'មានអតិបរមា :count :relationship',
                    'inverse' => 'មានច្រើនជាង :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'មានអប្បបរមា',
                    'inverse' => 'មានតិចជាង',
                ],

                'summary' => [
                    'direct' => 'មានអប្បបរមា :count :relationship',
                    'inverse' => 'មានតិចជាង :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'គឺ​ទទេ',
                    'inverse' => 'មិនទទេ',
                ],

                'summary' => [
                    'direct' => ':relationship គឺ​ទទេ',
                    'inverse' => ':relationship មិនទទេ',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'គឺ',
                        'inverse' => 'មិន​មែន',
                    ],

                    'multiple' => [
                        'direct' => 'មាន',
                        'inverse' => 'មិនមាន',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship គឺ :values',
                        'inverse' => ':relationship មិន​មែន :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship មាន :values',
                        'inverse' => ':relationship មិនមាន :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' ឬ ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'តម្លៃ',
                    ],

                    'values' => [
                        'label' => 'តម្លៃច្រើន',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'រាប់',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'គឺ',
                    'inverse' => 'មិន​មែន',
                ],

                'summary' => [
                    'direct' => ':attribute គឺ :values',
                    'inverse' => ':attribute មិន​មែន :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' ឬ ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'តម្លៃ',
                    ],

                    'values' => [
                        'label' => 'តម្លៃច្រើន',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'មាន',
                    'inverse' => 'មិនមែន',
                ],

                'summary' => [
                    'direct' => ':attribute មាន :text',
                    'inverse' => ':attribute មិនមែន :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'បញ្ចប់ដោយ',
                    'inverse' => 'មិនបញ្ចប់ដោយ',
                ],

                'summary' => [
                    'direct' => ':attribute បញ្ចប់ដោយ :text',
                    'inverse' => ':attribute មិនបញ្ចប់ដោយ :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'ស្មើ',
                    'inverse' => 'មិនស្មើគ្នា',
                ],

                'summary' => [
                    'direct' => ':attribute ស្មើ :text',
                    'inverse' => ':attribute មិនស្មើគ្នា :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'ចាប់ផ្តើមជាមួយ',
                    'inverse' => 'មិនចាប់ផ្តើមជាមួយ',
                ],

                'summary' => [
                    'direct' => ':attribute ចាប់ផ្តើមជាមួយ :text',
                    'inverse' => ':attribute មិនចាប់ផ្តើមជាមួយ :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'អត្ថបទ',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'បន្ថែមច្បាប់',
        ],

        'add_rule_group' => [
            'label' => 'បន្ថែមក្រុមច្បាប់',
        ],

    ],

];
