<?php

return [

    'label' => 'دروستکردنی Query',

    'form' => [

        'operator' => [
            'label' => 'کردار',
        ],

        'or_groups' => [

            'label' => 'گرووپەکان',

            'group' => [
                'label' => 'گرووپ',
            ],

            'block' => [
                'label' => 'جیاکردنەوە (یان)',
                'picker_group' => 'لۆجیک',
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

    'max_rules_reached_tooltip' => 'گەیشتوویتە زۆرترین ژمارەی ڕێساکان، کە :count ڕێسایە.',

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

            'unit_labels' => [
                'second' => 'چرکە',
                'minute' => 'خولەک',
                'hour' => 'کاتژمێر',
                'day' => 'ڕۆژ',
                'week' => 'هەفتە',
                'month' => 'مانگ',
                'quarter' => 'چارەک',
                'year' => 'ساڵ',
            ],

            'presets' => [
                'past_decade' => 'دەیەی ڕابردوو',
                'past_5_years' => '5 ساڵی ڕابردوو',
                'past_2_years' => '2 ساڵی ڕابردوو',
                'past_year' => 'ساڵی ڕابردوو',
                'past_6_months' => '6 مانگی ڕابردوو',
                'past_quarter' => 'چارەکی ڕابردوو',
                'past_month' => 'مانگی ڕابردوو',
                'past_2_weeks' => '2 هەفتەی ڕابردوو',
                'past_week' => 'هەفتەی ڕابردوو',
                'past_hour' => 'کاتژمێری ڕابردوو',
                'past_minute' => 'خولەکی ڕابردوو',
                'this_decade' => 'ئەم دەیەیە',
                'this_year' => 'ئەمساڵ',
                'this_quarter' => 'ئەم چارەکە',
                'this_month' => 'ئەم مانگە',
                'today' => 'ئەمڕۆ',
                'this_hour' => 'ئەم کاتژمێرە',
                'this_minute' => 'ئەم خولەکە',
                'next_minute' => 'خولەکی داهاتوو',
                'next_hour' => 'کاتژمێری داهاتوو',
                'next_week' => 'هەفتەی داهاتوو',
                'next_2_weeks' => '2 هەفتەی داهاتوو',
                'next_month' => 'مانگی داهاتوو',
                'next_quarter' => 'چارەکی داهاتوو',
                'next_6_months' => '6 مانگی داهاتوو',
                'next_year' => 'ساڵی داهاتوو',
                'next_2_years' => '2 ساڵی داهاتوو',
                'next_5_years' => '5 ساڵی داهاتوو',
                'next_decade' => 'دەیەی داهاتوو',
                'custom' => 'دڵخواز',
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

                'mode' => [

                    'label' => 'جۆری بەروار',

                    'options' => [
                        'absolute' => 'بەرواری دیاریکراو',
                        'relative' => 'ماوەی ڕێژەیی',
                    ],

                ],

                'preset' => [
                    'label' => 'ماوەی کات',
                ],

                'relative_value' => [
                    'label' => 'چەند',
                ],

                'relative_unit' => [
                    'label' => 'یەکەی کات',
                ],

                'tense' => [

                    'label' => 'ئاڕاستەی کات',

                    'options' => [
                        'past' => 'ڕابردوو',
                        'future' => 'داهاتوو',
                    ],

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
