<?php

return [

    'label' => 'কোয়েরি নির্মাতা',

    'form' => [

        'operator' => [
            'label' => 'অপারেটর',
        ],

        'or_groups' => [

            'label' => 'গ্রুপ',

            'block' => [
                'label' => 'অথবা শর্ত',
                'or' => 'অথবা',
            ],

        ],

        'rules' => [

            'label' => 'নিয়ম',

            'item' => [
                'and' => 'এবং',
            ],

        ],

    ],

    'no_rules' => '(কোন নিয়ম নেই)',

    'item_separators' => [
        'and' => 'এবং',
        'or' => 'অথবা',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'পূর্ণ আছে',
                'inverse' => 'খালি আছে',
            ],

            'summary' => [
                'direct' => ':attribute পূর্ণ আছে',
                'inverse' => ':attribute খালি আছে',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'সত্য',
                    'inverse' => 'মিথ্যা',
                ],

                'summary' => [
                    'direct' => ':attribute সত্য',
                    'inverse' => ':attribute মিথ্যা',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'পরে',
                    'inverse' => 'পরে নয়',
                ],

                'summary' => [
                    'direct' => ':attribute :date এর পরে',
                    'inverse' => ':attribute :date এর পরে নয়',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'আগে',
                    'inverse' => 'আগে নয়',
                ],

                'summary' => [
                    'direct' => ':attribute :date এর আগে',
                    'inverse' => ':attribute :date এর আগে নয়',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'তারিখ',
                    'inverse' => 'তারিখ নয়',
                ],

                'summary' => [
                    'direct' => ':attribute :date',
                    'inverse' => ':attribute :date নয়',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'মাস',
                    'inverse' => 'মাস নয়',
                ],

                'summary' => [
                    'direct' => ':attribute :month',
                    'inverse' => ':attribute :month নয়',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'বছর',
                    'inverse' => 'বছর নয়',
                ],

                'summary' => [
                    'direct' => ':attribute :year',
                    'inverse' => ':attribute :year নয়',
                ],

            ],

            'unit_labels' => [
                'second' => 'সেকেন্ড',
                'minute' => 'মিনিট',
                'hour' => 'ঘন্টা',
                'day' => 'দিন',
                'week' => 'সপ্তাহ',
                'month' => 'মাস',
                'quarter' => 'ত্রৈমাসিক',
                'year' => 'বছর',
            ],

            'presets' => [
                'past_decade' => 'গত দশক',
                'past_5_years' => 'গত ৫ বছর',
                'past_2_years' => 'গত ২ বছর',
                'past_year' => 'গত বছর',
                'past_6_months' => 'গত ৬ মাস',
                'past_quarter' => 'গত ত্রৈমাসিক',
                'past_month' => 'গত মাস',
                'past_2_weeks' => 'গত ২ সপ্তাহ',
                'past_week' => 'গত সপ্তাহ',
                'past_hour' => 'গত এক ঘন্টা',
                'past_minute' => 'গত এক মিনিট',
                'this_decade' => 'এই দশক',
                'this_year' => 'এই বছর',
                'this_quarter' => 'এই ত্রৈমাসিক',
                'this_month' => 'এই মাস',
                'today' => 'আজ',
                'this_hour' => 'এই ঘন্টা',
                'this_minute' => 'এই মিনিট',
                'next_minute' => 'পরবর্তী মিনিট',
                'next_hour' => 'পরবর্তী ঘন্টা',
                'next_week' => 'পরবর্তী সপ্তাহ',
                'next_2_weeks' => 'পরবর্তী ২ সপ্তাহ',
                'next_month' => 'পরবর্তী মাস',
                'next_quarter' => 'পরবর্তী ত্রৈমাসিক',
                'next_6_months' => 'পরবর্তী ৬ মাস',
                'next_year' => 'পরবর্তী বছর',
                'next_2_years' => 'পরবর্তী ২ বছর',
                'next_5_years' => 'পরবর্তী ৫ বছর',
                'next_decade' => 'পরবর্তী দশক',
                'custom' => 'নিজস্ব',
            ],

            'form' => [

                'date' => [
                    'label' => 'তারিখ',
                ],

                'month' => [
                    'label' => 'মাস',
                ],

                'year' => [
                    'label' => 'বছর',
                ],

                'mode' => [

                    'label' => 'তারিখের ধরন',

                    'options' => [
                        'absolute' => 'নির্দিষ্ট তারিখ',
                        'relative' => 'রোলিং উইন্ডো',
                    ],

                ],

                'preset' => [
                    'label' => 'সময়কাল',
                ],

                'relative_value' => [
                    'label' => 'কতগুলো',
                ],

                'relative_unit' => [
                    'label' => 'সময়ের একক',
                ],

                'tense' => [

                    'label' => 'কাল',

                    'options' => [
                        'past' => 'অতীত',
                        'future' => 'ভবিষ্যৎ',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'সমান',
                    'inverse' => 'সমান নয়',
                ],

                'summary' => [
                    'direct' => ':attribute :number এর সমান',
                    'inverse' => ':attribute :number এর সমান নয়',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'সর্বোচ্চ',
                    'inverse' => 'এর চেয়ে বড়',
                ],

                'summary' => [
                    'direct' => ':attribute সর্বোচ্চ :number',
                    'inverse' => ':attribute :number এর চেয়ে বড়',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'সর্বনিম্ন',
                    'inverse' => 'এর চেয়ে ছোট',
                ],

                'summary' => [
                    'direct' => ':attribute সর্বনিম্ন :number',
                    'inverse' => ':attribute :number এর চেয়ে ছোট',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'গড়',
                    'summary' => 'গড় :attribute',
                ],

                'max' => [
                    'label' => 'সর্বোচ্চ',
                    'summary' => 'সর্বোচ্চ :attribute',
                ],

                'min' => [
                    'label' => 'সর্বনিম্ন',
                    'summary' => 'সর্বনিম্ন :attribute',
                ],

                'sum' => [
                    'label' => 'সমষ্টি',
                    'summary' => ':attribute এর সমষ্টি',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'সমষ্টিগত',
                ],

                'number' => [
                    'label' => 'সংখ্যা',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'আছে',
                    'inverse' => 'নেই',
                ],

                'summary' => [
                    'direct' => ':count টি :relationship আছে',
                    'inverse' => ':count টি :relationship নেই',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'সর্বোচ্চ আছে',
                    'inverse' => 'এর চেয়ে বেশি আছে',
                ],

                'summary' => [
                    'direct' => 'সর্বোচ্চ :count টি :relationship আছে',
                    'inverse' => ':count টি এর চেয়ে বেশি :relationship আছে',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'সর্বনিম্ন আছে',
                    'inverse' => 'এর চেয়ে কম আছে',
                ],

                'summary' => [
                    'direct' => 'সর্বনিম্ন :count টি :relationship আছে',
                    'inverse' => ':count টি এর চেয়ে কম :relationship আছে',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'খালি',
                    'inverse' => 'খালি নয়',
                ],

                'summary' => [
                    'direct' => ':relationship খালি',
                    'inverse' => ':relationship খালি নয়',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'হয়',
                        'inverse' => 'নয়',
                    ],

                    'multiple' => [
                        'direct' => 'অন্তর্ভুক্ত আছে',
                        'inverse' => 'অন্তর্ভুক্ত নেই',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship হলো :values',
                        'inverse' => ':relationship :values নয়',
                    ],

                    'multiple' => [
                        'direct' => ':relationship এ :values আছে',
                        'inverse' => ':relationship এ :values নেই',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' অথবা ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'মান',
                    ],

                    'values' => [
                        'label' => 'মানসমূহ',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'সংখ্যা',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'হয়',
                    'inverse' => 'নয়',
                ],

                'summary' => [
                    'direct' => ':attribute হলো :values',
                    'inverse' => ':attribute :values নয়',
                    'values_glue' => [
                        ', ',
                        'final' => ' অথবা ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'মান',
                    ],

                    'values' => [
                        'label' => 'মানসমূহ',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'অন্তর্ভুক্ত আছে',
                    'inverse' => 'অন্তর্ভুক্ত নেই',
                ],

                'summary' => [
                    'direct' => ':attribute এ :text আছে',
                    'inverse' => ':attribute এ :text নেই',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'দিয়ে শেষ হয়',
                    'inverse' => 'দিয়ে শেষ হয় না',
                ],

                'summary' => [
                    'direct' => ':attribute :text দিয়ে শেষ হয়',
                    'inverse' => ':attribute :text দিয়ে শেষ হয় না',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'সমান',
                    'inverse' => 'সমান নয়',
                ],

                'summary' => [
                    'direct' => ':attribute :text এর সমান',
                    'inverse' => ':attribute :text এর সমান নয়',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'দিয়ে শুরু হয়',
                    'inverse' => 'দিয়ে শুরু হয় না',
                ],

                'summary' => [
                    'direct' => ':attribute :text দিয়ে শুরু হয়',
                    'inverse' => ':attribute :text দিয়ে শুরু হয় না',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'লেখা',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'নিয়ম যোগ করুন',
        ],

        'add_rule_group' => [
            'label' => 'অথবা যোগ করুন',
        ],

    ],

];
