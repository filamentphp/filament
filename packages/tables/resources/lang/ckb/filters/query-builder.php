<?php

return [

    'label' => 'دروستکردنی Query',

    'form' => [

        'operator' => [
            'label' => 'کردار',
        ],

        'or_groups' => [

            'label' => 'گرووپەکان',

            'block' => [
                'label' => 'جیاکردنەوە (یان)',
                'or' => 'یان',
            ],

        ],

        'rules' => [

            'label' => 'ڕێساکان',

            'item' => [
                'and' => 'لەگەڵ',
            ],

        ],

    ],

    'no_rules' => '(هیچ ڕێسایەک نیە)',

    'item_separators' => [
        'and' => 'لەگەڵ',
        'or' => 'یان',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'پڕکراوەتەوە',
                'inverse' => 'بەتاڵە',
            ],

            'summary' => [
                'direct' => ':attribute پڕکراوەتەوە',
                'inverse' => ':attribute بەتاڵە',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'چالاکە',
                    'inverse' => 'ناچالاکە',
                ],

                'summary' => [
                    'direct' => ':attribute چالاکە',
                    'inverse' => ':attribute ناچالاکە',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'لەپاش',
                    'inverse' => 'لەپاش نەبێت',
                ],

                'summary' => [
                    'direct' => ':attribute لەدوای :date',
                    'inverse' => ':attribute لەپاش :date نەبێت',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'لەپێش',
                    'inverse' => 'لەپێش نەبێت',
                ],

                'summary' => [
                    'direct' => ':attribute لەپێش :date',
                    'inverse' => ':attribute لەپێش :date نەبێت',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'لە بەرواری',
                    'inverse' => 'لە بەرواری نەبێت',
                ],

                'summary' => [
                    'direct' => ':attribute لە :date',
                    'inverse' => ':attribute لە :date نەبێت',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'لە مانگی',
                    'inverse' => 'لە مانگی نەبێت',
                ],

                'summary' => [
                    'direct' => ':attribute لە :month',
                    'inverse' => ':attribute لە :month نەبێت',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'لە ساڵی',
                    'inverse' => 'لە ساڵی نەبێت',
                ],

                'summary' => [
                    'direct' => ':attribute لە :year',
                    'inverse' => ':attribute لە :year نەبێت',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'بەروار',
                ],

                'month' => [
                    'label' => 'مانگ',
                ],

                'year' => [
                    'label' => 'ساڵ',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'یەکسان',
                    'inverse' => 'یەکسان نەبێت',
                ],

                'summary' => [
                    'direct' => ':attribute یەکسانە بە :number',
                    'inverse' => ':attribute یەکسان نیە بە :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'زۆرترینە',
                    'inverse' => 'گەورەترە لە',
                ],

                'summary' => [
                    'direct' => ':attribute زۆرترە لە :number',
                    'inverse' => ':attribute گەورەترە لە :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'کەمترە',
                    'inverse' => 'کەمترە لە',
                ],

                'summary' => [
                    'direct' => ':attribute کەمترە لە :number',
                    'inverse' => ':attribute کەمترە لە :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'ڕێژە',
                    'summary' => 'ڕێژەی :attribute',
                ],

                'max' => [
                    'label' => 'زۆرترین',
                    'summary' => 'زۆرترە لە :attribute',
                ],

                'min' => [
                    'label' => 'کەمترین',
                    'summary' => 'کەمترە لە :attribute',
                ],

                'sum' => [
                    'label' => 'کۆ',
                    'summary' => 'کۆی :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'کۆکراوە',
                ],

                'number' => [
                    'label' => 'ژمارە',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'هەیە',
                    'inverse' => 'نیە',
                ],

                'summary' => [
                    'direct' => ':count :relationship هەیە',
                    'inverse' => ':count :relationship نیە',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'زۆرترینی هەیە',
                    'inverse' => 'زیاتری هەیە لە',
                ],

                'summary' => [
                    'direct' => 'زۆرترین :count :relationship هەیە',
                    'inverse' => 'زیاتری هەیە لە :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'کەمترینی هەیە',
                    'inverse' => 'کەمتری هەیە',
                ],

                'summary' => [
                    'direct' => 'کەمترین :count :relationship هەیە',
                    'inverse' => 'کەمتری هەیە لە :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'بەتاڵە',
                    'inverse' => 'بەتاڵ نیە',
                ],

                'summary' => [
                    'direct' => ':relationship بەتاڵە',
                    'inverse' => ':relationship بەتاڵ نیە',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'بریتییە',
                        'inverse' => 'بریتی نیە',
                    ],

                    'multiple' => [
                        'direct' => 'لەخۆدەگرێت',
                        'inverse' => 'لەخۆناگرێت',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship بریتییە :values',
                        'inverse' => ':relationship بریتی نیە لە :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship لەخۆدەگرێت :values',
                        'inverse' => ':relationship لەخۆناگرێت :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' یان ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'بەها',
                    ],

                    'values' => [
                        'label' => 'بەهاکان',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'ژماردن',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'بریتییە',
                    'inverse' => 'بریتی نیە',
                ],

                'summary' => [
                    'direct' => ':attribute بریتییە لە :values',
                    'inverse' => ':attribute بریتی نیە لە :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' یان ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'بەها',
                    ],

                    'values' => [
                        'label' => 'بەهاکان',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'لەخۆدەگرێت',
                    'inverse' => 'لەخۆناگرێت',
                ],

                'summary' => [
                    'direct' => ':attribute :text لەخۆدەگرێت',
                    'inverse' => ':attribute :text لەخۆناگرێت',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'کۆتایی دێت بە',
                    'inverse' => 'کۆتایی نایە بە',
                ],

                'summary' => [
                    'direct' => ':attribute کۆتایی دێت بە :text',
                    'inverse' => ':attribute کۆتایی نایە بە :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'یەکسانە',
                    'inverse' => 'یەکسان نیە',
                ],

                'summary' => [
                    'direct' => ':attribute یەکسانە بە :text',
                    'inverse' => ':attribute یەکسان نیە بە :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'دەستپێدەکات بە',
                    'inverse' => 'دەستپێناکات بە',
                ],

                'summary' => [
                    'direct' => ':attribute دەستپێدەکات بە :text',
                    'inverse' => ':attribute دەستپێناکات بە :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'دەق',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'زیادکردنی ڕێسا',
        ],

        'add_rule_group' => [
            'label' => 'زیادکردنی گرووپی ڕێسا',
        ],

    ],

];
