<?php

return [

    'label' => 'سازنده کوئری',

    'form' => [

        'operator' => [
            'label' => 'عملگر',
        ],

        'or_groups' => [

            'label' => 'گروه‌ها',

            'block' => [
                'label' => 'تفکیک (OR)',
                'or' => 'یا',
            ],

        ],

        'rules' => [

            'label' => 'قوانین',

            'item' => [
                'and' => 'و',
            ],

        ],

    ],

    'no_rules' => '(بدون قانون)',

    'item_separators' => [
        'and' => 'و',
        'or' => 'یا',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'پر است',
                'inverse' => 'خالی است',
            ],

            'summary' => [
                'direct' => ':attribute پر است',
                'inverse' => ':attribute خالی است',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'درست است',
                    'inverse' => 'نادرست است',
                ],

                'summary' => [
                    'direct' => ':attribute درست است',
                    'inverse' => ':attribute نادرست است',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'بعد از',
                    'inverse' => 'بعد از نیست',
                ],

                'summary' => [
                    'direct' => ':attribute بعد از :date است',
                    'inverse' => ':attribute بعد از :date نیست',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'قبل از',
                    'inverse' => 'قبل از نیست',
                ],

                'summary' => [
                    'direct' => ':attribute قبل از :date است',
                    'inverse' => ':attribute قبل از :date نیست',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'تاریخ است',
                    'inverse' => 'تاریخ نیست',
                ],

                'summary' => [
                    'direct' => ':attribute :date است',
                    'inverse' => ':attribute :date نیست',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'ماه است',
                    'inverse' => 'ماه نیست',
                ],

                'summary' => [
                    'direct' => ':attribute :month است',
                    'inverse' => ':attribute :month نیست',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'سال است',
                    'inverse' => 'سال نیست',
                ],

                'summary' => [
                    'direct' => ':attribute :year است',
                    'inverse' => ':attribute :year نیست',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'تاریخ',
                ],

                'month' => [
                    'label' => 'ماه',
                ],

                'year' => [
                    'label' => 'سال',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'مساوی است',
                    'inverse' => 'مساوی نیست',
                ],

                'summary' => [
                    'direct' => ':attribute مساوی :number است',
                    'inverse' => ':attribute مساوی :number نیست',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'حداکثر است',
                    'inverse' => 'بیشتر از',
                ],

                'summary' => [
                    'direct' => ':attribute حداکثر :number است',
                    'inverse' => ':attribute بیشتر از :number است',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'حداقل است',
                    'inverse' => 'کمتر از',
                ],

                'summary' => [
                    'direct' => ':attribute حداقل :number است',
                    'inverse' => ':attribute کمتر از :number است',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'میانگین',
                    'summary' => 'میانگین :attribute',
                ],

                'max' => [
                    'label' => 'حداکثر',
                    'summary' => 'حداکثر :attribute',
                ],

                'min' => [
                    'label' => 'حداقل',
                    'summary' => 'حداقل :attribute',
                ],

                'sum' => [
                    'label' => 'جمع',
                    'summary' => 'جمع :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'مجموع',
                ],

                'number' => [
                    'label' => 'عدد',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'دارد',
                    'inverse' => 'ندارد',
                ],

                'summary' => [
                    'direct' => ':count :relationship دارد',
                    'inverse' => ':count :relationship ندارد',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'حداکثر دارد',
                    'inverse' => 'بیشتر از',
                ],

                'summary' => [
                    'direct' => 'حداکثر :count :relationship دارد',
                    'inverse' => 'بیشتر از :count :relationship دارد',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'حداقل دارد',
                    'inverse' => 'کمتر از',
                ],

                'summary' => [
                    'direct' => 'حداقل :count :relationship دارد',
                    'inverse' => 'کمتر از :count :relationship دارد',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'خالی است',
                    'inverse' => 'خالی نیست',
                ],

                'summary' => [
                    'direct' => ':relationship خالی است',
                    'inverse' => ':relationship خالی نیست',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'است',
                        'inverse' => 'نیست',
                    ],

                    'multiple' => [
                        'direct' => 'شامل',
                        'inverse' => 'شامل نمی‌شود',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship :values است',
                        'inverse' => ':relationship :values نیست',
                    ],

                    'multiple' => [
                        'direct' => ':relationship شامل :values است',
                        'inverse' => ':relationship شامل :values نمی‌شود',
                    ],

                    'values_glue' => [
                        0 => '، ',
                        'final' => ' یا ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'مقدار',
                    ],

                    'values' => [
                        'label' => 'مقادیر',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'تعداد',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'است',
                    'inverse' => 'نیست',
                ],

                'summary' => [
                    'direct' => ':attribute :values است',
                    'inverse' => ':attribute :values نیست',
                    'values_glue' => [
                        '، ',
                        'final' => ' یا ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'مقدار',
                    ],

                    'values' => [
                        'label' => 'مقادیر',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'شامل',
                    'inverse' => 'شامل نمی‌شود',
                ],

                'summary' => [
                    'direct' => ':attribute شامل :text است',
                    'inverse' => ':attribute شامل :text نمی‌شود',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'به پایان می‌رسد با',
                    'inverse' => 'به پایان نمی‌رسد با',
                ],

                'summary' => [
                    'direct' => ':attribute به پایان می‌رسد با :text',
                    'inverse' => ':attribute به پایان نمی‌رسد با :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'مساوی است',
                    'inverse' => 'مساوی نیست',
                ],

                'summary' => [
                    'direct' => ':attribute مساوی :text است',
                    'inverse' => ':attribute مساوی :text نیست',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'شروع می‌شود با',
                    'inverse' => 'شروع نمی‌شود با',
                ],

                'summary' => [
                    'direct' => ':attribute شروع می‌شود با :text',
                    'inverse' => ':attribute شروع نمی‌شود با :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'متن',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'افزودن قانون',
        ],

        'add_rule_group' => [
            'label' => 'افزودن گروه قانون',
        ],

    ],

];
