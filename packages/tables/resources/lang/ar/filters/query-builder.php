<?php

return [

    'label' => 'منشئ الاستعلام',

    'form' => [

        'operator' => [
            'label' => 'العملية',
        ],

        'or_groups' => [

            'label' => 'المجموعات',

            'block' => [
                'label' => 'فصل (أو)',
                'or' => 'أو',
            ],

        ],

        'rules' => [

            'label' => 'القواعد',

            'item' => [
                'and' => 'و',
            ],

        ],

    ],

    'no_rules' => '(بدون قواعد)',

    'item_separators' => [
        'and' => 'أو',
        'or' => 'و',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'معبأ',
                'inverse' => 'فارغ',
            ],

            'summary' => [
                'direct' => ':attribute معبأ',
                'inverse' => ':attribute فارغ',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'صحيح',
                    'inverse' => 'خاطئ',
                ],

                'summary' => [
                    'direct' => ':attribute صحيح',
                    'inverse' => ':attribute خاطئ',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'بعد',
                    'inverse' => 'ليس بعد',
                ],

                'summary' => [
                    'direct' => ':attribute بعد :date',
                    'inverse' => ':attribute ليس بعد :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'قبل',
                    'inverse' => 'ليس قبل',
                ],

                'summary' => [
                    'direct' => ':attribute قبل :date',
                    'inverse' => ':attribute ليس قبل :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'تاريخ',
                    'inverse' => 'ليس تاريخ',
                ],

                'summary' => [
                    'direct' => ':attribute هو :date',
                    'inverse' => ':attribute هو ليس :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'شهر',
                    'inverse' => 'ليس شهر',
                ],

                'summary' => [
                    'direct' => ':attribute هو :month',
                    'inverse' => ':attribute هو ليس :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'سنة',
                    'inverse' => 'ليس سنة',
                ],

                'summary' => [
                    'direct' => ':attribute هو :year',
                    'inverse' => ':attribute هو ليس :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'التاريخ',
                ],

                'month' => [
                    'label' => 'الشهر',
                ],

                'year' => [
                    'label' => 'السنة',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'يساوي',
                    'inverse' => 'لا يساوي',
                ],

                'summary' => [
                    'direct' => ':attribute يساوي :number',
                    'inverse' => ':attribute لا يساوي :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'الحد الأقصى',
                    'inverse' => 'أكبر من',
                ],

                'summary' => [
                    'direct' => ':attribute حده الأقصى :number',
                    'inverse' => ':attribute أكبر من :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'الحد الأدنى',
                    'inverse' => 'أقل من',
                ],

                'summary' => [
                    'direct' => ':attribute حده الأدنى :number',
                    'inverse' => ':attribute أقل من :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'المتوسط',
                    'summary' => 'متوسط :attribute',
                ],

                'max' => [
                    'label' => 'الأعلى',
                    'summary' => 'الأعلى :attribute',
                ],

                'min' => [
                    'label' => 'الأدنى',
                    'summary' => 'الأدنى :attribute',
                ],

                'sum' => [
                    'label' => 'المجموع',
                    'summary' => 'مجموع :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'الإجمالي',
                ],

                'number' => [
                    'label' => 'الرقم',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'يملك',
                    'inverse' => 'لا يملك',
                ],

                'summary' => [
                    'direct' => 'يملك :count :relationship',
                    'inverse' => 'لا يملك :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'يملك الحد الأقصى',
                    'inverse' => 'يملك أكثر من',
                ],

                'summary' => [
                    'direct' => 'يملك كحد أقصى :count :relationship',
                    'inverse' => 'يملك أكثر من :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'يملك الحد الأدنى',
                    'inverse' => 'يملك أقل من',
                ],

                'summary' => [
                    'direct' => 'يملك كحد أدنى :count :relationship',
                    'inverse' => 'يملك أقل من :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'فارغ',
                    'inverse' => 'ليس فارغا',
                ],

                'summary' => [
                    'direct' => ':relationship فارغ',
                    'inverse' => ':relationship ليس فارغاً',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'هو',
                        'inverse' => 'ليس',
                    ],

                    'multiple' => [
                        'direct' => 'يحتوي',
                        'inverse' => 'لا يحتوي',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship هو :values',
                        'inverse' => ':relationship ليس :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship يحتوي :values',
                        'inverse' => ':relationship لا يحتوي :values',
                    ],

                    'values_glue' => [
                        0 => '، ',
                        'final' => ' أو ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'القيمة',
                    ],

                    'values' => [
                        'label' => 'القيم',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'العدد',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'هو',
                    'inverse' => 'ليس',
                ],

                'summary' => [
                    'direct' => ':attribute هو :values',
                    'inverse' => ':attribute ليس :values',
                    'values_glue' => [
                        ', ' => '، ',
                        'final' => ' أو ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'القيمة',
                    ],

                    'values' => [
                        'label' => 'القيم',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'يحتوي',
                    'inverse' => 'لا يحتوي',
                ],

                'summary' => [
                    'direct' => ':attribute يحتوي :text',
                    'inverse' => ':attribute لا يحتوي :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'ينتهي بـ',
                    'inverse' => 'لا ينتهي بـ',
                ],

                'summary' => [
                    'direct' => ':attribute ينتهي بـ :text',
                    'inverse' => ':attribute لا ينتهي بـ :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'يساوي',
                    'inverse' => 'لا يساوي',
                ],

                'summary' => [
                    'direct' => ':attribute يساوي :text',
                    'inverse' => ':attribute لا يساوي :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'يبدأ بـ',
                    'inverse' => 'لا يبدأ بـ',
                ],

                'summary' => [
                    'direct' => ':attribute يبدأ بـ :text',
                    'inverse' => ':attribute لا يبدأ بـ :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'النص',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'إضافة قاعدة',
        ],

        'add_rule_group' => [
            'label' => 'إضافة مجموعة قواعد',
        ],

    ],

];
