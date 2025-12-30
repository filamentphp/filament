<?php

return [

    'label' => 'کوئری بلڈر',

    'form' => [

        'operator' => [
            'label' => 'آپریٹر',
        ],

        'or_groups' => [

            'label' => 'گروپس',

            'block' => [
                'label' => 'یا (OR)',
                'or' => 'یا',
            ],

        ],

        'rules' => [

            'label' => 'قوانین',

            'item' => [
                'and' => 'اور',
            ],

        ],

    ],

    'no_rules' => '(کوئی قانون نہیں)',

    'item_separators' => [
        'and' => 'اور',
        'or' => 'یا',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'بھرا ہوا ہے',
                'inverse' => 'خالی ہے',
            ],

            'summary' => [
                'direct' => ':attribute بھرا ہوا ہے',
                'inverse' => ':attribute خالی ہے',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'سچ ہے',
                    'inverse' => 'جھوٹ ہے',
                ],

                'summary' => [
                    'direct' => ':attribute سچ ہے',
                    'inverse' => ':attribute جھوٹ ہے',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'کے بعد ہے',
                    'inverse' => 'کے بعد نہیں ہے',
                ],

                'summary' => [
                    'direct' => ':attribute :date کے بعد ہے',
                    'inverse' => ':attribute :date کے بعد نہیں ہے',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'سے پہلے ہے',
                    'inverse' => 'سے پہلے نہیں ہے',
                ],

                'summary' => [
                    'direct' => ':attribute :date سے پہلے ہے',
                    'inverse' => ':attribute :date سے پہلے نہیں ہے',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'تاریخ ہے',
                    'inverse' => 'تاریخ نہیں ہے',
                ],

                'summary' => [
                    'direct' => ':attribute :date ہے',
                    'inverse' => ':attribute :date نہیں ہے',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'مہینہ ہے',
                    'inverse' => 'مہینہ نہیں ہے',
                ],

                'summary' => [
                    'direct' => ':attribute :month ہے',
                    'inverse' => ':attribute :month نہیں ہے',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'سال ہے',
                    'inverse' => 'سال نہیں ہے',
                ],

                'summary' => [
                    'direct' => ':attribute :year ہے',
                    'inverse' => ':attribute :year نہیں ہے',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'تاریخ',
                ],

                'month' => [
                    'label' => 'مہینہ',
                ],

                'year' => [
                    'label' => 'سال',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'برابر ہے',
                    'inverse' => 'برابر نہیں ہے',
                ],

                'summary' => [
                    'direct' => ':attribute :number کے برابر ہے',
                    'inverse' => ':attribute :number کے برابر نہیں ہے',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'زیادہ سے زیادہ ہے',
                    'inverse' => 'اس سے زیادہ ہے',
                ],

                'summary' => [
                    'direct' => ':attribute زیادہ سے زیادہ :number ہے',
                    'inverse' => ':attribute :number سے زیادہ ہے',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'کم سے کم ہے',
                    'inverse' => 'اس سے کم ہے',
                ],

                'summary' => [
                    'direct' => ':attribute کم سے کم :number ہے',
                    'inverse' => ':attribute :number سے کم ہے',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'اوسط',
                    'summary' => ':attribute کی اوسط',
                ],

                'max' => [
                    'label' => 'زیادہ سے زیادہ',
                    'summary' => ':attribute کا زیادہ سے زیادہ',
                ],

                'min' => [
                    'label' => 'کم سے کم',
                    'summary' => ':attribute کا کم سے کم',
                ],

                'sum' => [
                    'label' => 'مجموعہ',
                    'summary' => ':attribute کا مجموعہ',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'مجموعی حساب',
                ],

                'number' => [
                    'label' => 'نمبر',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'ہے',
                    'inverse' => 'نہیں ہے',
                ],

                'summary' => [
                    'direct' => ':count :relationship ہے',
                    'inverse' => ':count :relationship نہیں ہے',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'زیادہ سے زیادہ ہے',
                    'inverse' => 'اس سے زیادہ ہے',
                ],

                'summary' => [
                    'direct' => 'زیادہ سے زیادہ :count :relationship ہے',
                    'inverse' => ':count سے زیادہ :relationship ہے',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'کم سے کم ہے',
                    'inverse' => 'اس سے کم ہے',
                ],

                'summary' => [
                    'direct' => 'کم سے کم :count :relationship ہے',
                    'inverse' => ':count سے کم :relationship ہے',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'خالی ہے',
                    'inverse' => 'خالی نہیں ہے',
                ],

                'summary' => [
                    'direct' => ':relationship خالی ہے',
                    'inverse' => ':relationship خالی نہیں ہے',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'ہے',
                        'inverse' => 'نہیں ہے',
                    ],

                    'multiple' => [
                        'direct' => 'پر مشتمل ہے',
                        'inverse' => 'پر مشتمل نہیں ہے',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship :values ہے',
                        'inverse' => ':relationship :values نہیں ہے',
                    ],

                    'multiple' => [
                        'direct' => ':relationship میں :values شامل ہے',
                        'inverse' => ':relationship میں :values شامل نہیں ہے',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' یا ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'قدر',
                    ],

                    'values' => [
                        'label' => 'قدریں',
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
                    'direct' => 'ہے',
                    'inverse' => 'نہیں ہے',
                ],

                'summary' => [
                    'direct' => ':attribute :values ہے',
                    'inverse' => ':attribute :values نہیں ہے',
                    'values_glue' => [
                        ', ',
                        'final' => ' یا ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'قدر',
                    ],

                    'values' => [
                        'label' => 'قدریں',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'پر مشتمل ہے',
                    'inverse' => 'پر مشتمل نہیں ہے',
                ],

                'summary' => [
                    'direct' => ':attribute میں :text شامل ہے',
                    'inverse' => ':attribute میں :text شامل نہیں ہے',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'پر ختم ہوتا ہے',
                    'inverse' => 'پر ختم نہیں ہوتا',
                ],

                'summary' => [
                    'direct' => ':attribute کا اختتام :text پر ہوتا ہے',
                    'inverse' => ':attribute کا اختتام :text پر نہیں ہوتا',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'برابر ہے',
                    'inverse' => 'برابر نہیں ہے',
                ],

                'summary' => [
                    'direct' => ':attribute :text کے برابر ہے',
                    'inverse' => ':attribute :text کے برابر نہیں ہے',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'سے شروع ہوتا ہے',
                    'inverse' => 'سے شروع نہیں ہوتا',
                ],

                'summary' => [
                    'direct' => ':attribute :text سے شروع ہوتا ہے',
                    'inverse' => ':attribute :text سے شروع نہیں ہوتا',
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
            'label' => 'قانون شامل کریں',
        ],

        'add_rule_group' => [
            'label' => 'قانونی گروپ شامل کریں',
        ],

    ],

];
