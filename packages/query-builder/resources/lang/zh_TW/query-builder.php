<?php

return [

    'label' => '查詢建構器',

    'form' => [

        'operator' => [
            'label' => '運算子',
        ],

        'or_groups' => [

            'label' => '條件群組',

            'block' => [
                'label' => '或條件群組 (OR)',
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
