<?php

return [

    'label' => '查詢建構器',

    'form' => [

        'operator' => [
            'label' => '運算子',
        ],

        'or_groups' => [

            'label' => '條件群組',

            'group' => [
                'label' => '群組',
            ],

            'block' => [
                'label' => '或條件群組 (OR)',
                'picker_group' => '邏輯',
                'or' => '或',
            ],

        ],

        'rules' => [

            'label' => '規則',

            'item' => [
                'and' => '且',
            ],

        ],

    ],

    'no_rules' => '（無規則）',

    'max_rules_reached_tooltip' => '規則數量已達上限（:count 條）。',

    'item_separators' => [
        'and' => '且',
        'or' => '或',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => '已填寫',
                'inverse' => '未填寫',
            ],

            'summary' => [
                'direct' => ':attribute 已填寫',
                'inverse' => ':attribute 未填寫',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => '為真',
                    'inverse' => '為假',
                ],

                'summary' => [
                    'direct' => ':attribute 為真',
                    'inverse' => ':attribute 為假',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => '晚於',
                    'inverse' => '不晚於',
                ],

                'summary' => [
                    'direct' => ':attribute 晚於 :date',
                    'inverse' => ':attribute 不晚於 :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => '早於',
                    'inverse' => '不早於',
                ],

                'summary' => [
                    'direct' => ':attribute 早於 :date',
                    'inverse' => ':attribute 不早於 :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => '等於日期',
                    'inverse' => '不等於日期',
                ],

                'summary' => [
                    'direct' => ':attribute 等於 :date',
                    'inverse' => ':attribute 不等於 :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => '等於月份',
                    'inverse' => '不等於月份',
                ],

                'summary' => [
                    'direct' => ':attribute 等於 :month',
                    'inverse' => ':attribute 不等於 :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => '等於年份',
                    'inverse' => '不等於年份',
                ],

                'summary' => [
                    'direct' => ':attribute 等於 :year',
                    'inverse' => ':attribute 不等於 :year',
                ],

            ],

            'unit_labels' => [
                'second' => '秒',
                'minute' => '分鐘',
                'hour' => '小時',
                'day' => '天',
                'week' => '週',
                'month' => '月',
                'quarter' => '季',
                'year' => '年',
            ],

            'presets' => [
                'past_decade' => '過去十年',
                'past_5_years' => '過去 5 年',
                'past_2_years' => '過去 2 年',
                'past_year' => '過去一年',
                'past_6_months' => '過去 6 個月',
                'past_quarter' => '過去一季',
                'past_month' => '過去一個月',
                'past_2_weeks' => '過去 2 週',
                'past_week' => '過去一週',
                'past_hour' => '過去一小時',
                'past_minute' => '過去一分鐘',
                'this_decade' => '這十年',
                'this_year' => '今年',
                'this_quarter' => '本季',
                'this_month' => '本月',
                'today' => '今天',
                'this_hour' => '這一小時',
                'this_minute' => '這一分鐘',
                'next_minute' => '未來一分鐘',
                'next_hour' => '未來一小時',
                'next_week' => '未來一週',
                'next_2_weeks' => '未來 2 週',
                'next_month' => '未來一個月',
                'next_quarter' => '未來一季',
                'next_6_months' => '未來 6 個月',
                'next_year' => '未來一年',
                'next_2_years' => '未來 2 年',
                'next_5_years' => '未來 5 年',
                'next_decade' => '未來十年',
                'custom' => '自訂',
            ],

            'form' => [

                'date' => [
                    'label' => '日期',
                ],

                'month' => [
                    'label' => '月份',
                ],

                'year' => [
                    'label' => '年份',
                ],

                'mode' => [

                    'label' => '日期類型',

                    'options' => [
                        'absolute' => '指定日期',
                        'relative' => '相對時間區間',
                    ],

                ],

                'preset' => [
                    'label' => '時間範圍',
                ],

                'relative_value' => [
                    'label' => '數量',
                ],

                'relative_unit' => [
                    'label' => '時間單位',
                ],

                'tense' => [

                    'label' => '時間方向',

                    'options' => [
                        'past' => '過去',
                        'future' => '未來',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => '等於',
                    'inverse' => '不等於',
                ],

                'summary' => [
                    'direct' => ':attribute 等於 :number',
                    'inverse' => ':attribute 不等於 :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => '最大為',
                    'inverse' => '大於',
                ],

                'summary' => [
                    'direct' => ':attribute 最大為 :number',
                    'inverse' => ':attribute 大於 :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => '最小為',
                    'inverse' => '小於',
                ],

                'summary' => [
                    'direct' => ':attribute 最小為 :number',
                    'inverse' => ':attribute 小於 :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => '平均值',
                    'summary' => ':attribute 平均值',
                ],

                'max' => [
                    'label' => '最大值',
                    'summary' => ':attribute 最大值',
                ],

                'min' => [
                    'label' => '最小值',
                    'summary' => ':attribute 最小值',
                ],

                'sum' => [
                    'label' => '總和',
                    'summary' => ':attribute 總和',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => '聚合函數',
                ],

                'number' => [
                    'label' => '數值',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => '包含',
                    'inverse' => '不包含',
                ],

                'summary' => [
                    'direct' => '包含 :count 個 :relationship',
                    'inverse' => '不包含 :count 個 :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => '最多包含',
                    'inverse' => '超過',
                ],

                'summary' => [
                    'direct' => '最多包含 :count 個 :relationship',
                    'inverse' => '超過 :count 個 :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => '至少包含',
                    'inverse' => '少於',
                ],

                'summary' => [
                    'direct' => '至少包含 :count 個 :relationship',
                    'inverse' => '少於 :count 個 :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => '為空',
                    'inverse' => '不為空',
                ],

                'summary' => [
                    'direct' => ':relationship 為空',
                    'inverse' => ':relationship 不為空',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => '等於',
                        'inverse' => '不等於',
                    ],

                    'multiple' => [
                        'direct' => '包含',
                        'inverse' => '不包含',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship 等於 :values',
                        'inverse' => ':relationship 不等於 :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship 包含 :values',
                        'inverse' => ':relationship 不包含 :values',
                    ],

                    'values_glue' => [
                        0 => '、',
                        'final' => ' 或 ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => '值',
                    ],

                    'values' => [
                        'label' => '值清單',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => '數量',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => '等於',
                    'inverse' => '不等於',
                ],

                'summary' => [
                    'direct' => ':attribute 等於 :values',
                    'inverse' => ':attribute 不等於 :values',
                    'values_glue' => [
                        '、',
                        'final' => ' 或 ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => '值',
                    ],

                    'values' => [
                        'label' => '值清單',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => '包含',
                    'inverse' => '不包含',
                ],

                'summary' => [
                    'direct' => ':attribute 包含 ":text"',
                    'inverse' => ':attribute 不包含 ":text"',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => '以...結尾',
                    'inverse' => '不以...結尾',
                ],

                'summary' => [
                    'direct' => ':attribute 以 ":text" 結尾',
                    'inverse' => ':attribute 不以 ":text" 結尾',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => '等於',
                    'inverse' => '不等於',
                ],

                'summary' => [
                    'direct' => ':attribute 等於 ":text"',
                    'inverse' => ':attribute 不等於 ":text"',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => '以...開頭',
                    'inverse' => '不以...開頭',
                ],

                'summary' => [
                    'direct' => ':attribute 以 ":text" 開頭',
                    'inverse' => ':attribute 不以 ":text" 開頭',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => '文字',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => '新增規則',
        ],

        'add_rule_group' => [
            'label' => '新增規則群組',
        ],

    ],

];
