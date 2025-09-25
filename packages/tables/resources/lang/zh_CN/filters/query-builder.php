<?php

return [

    'label' => '查询构建器',

    'form' => [

        'operator' => [
            'label' => '运算符',
        ],

        'or_groups' => [

            'label' => '条件组',

            'block' => [
                'label' => '或条件组 (OR)',
                'or' => '或',
            ],

        ],

        'rules' => [

            'label' => '规则',

            'item' => [
                'and' => '且',
            ],

        ],

    ],

    'no_rules' => '（无规则）',

    'item_separators' => [
        'and' => '且',
        'or' => '或',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => '已填写',
                'inverse' => '未填写',
            ],

            'summary' => [
                'direct' => ':attribute 已填写',
                'inverse' => ':attribute 未填写',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => '为真',
                    'inverse' => '为假',
                ],

                'summary' => [
                    'direct' => ':attribute 为真',
                    'inverse' => ':attribute 为假',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => '晚于',
                    'inverse' => '不晚于',
                ],

                'summary' => [
                    'direct' => ':attribute 晚于 :date',
                    'inverse' => ':attribute 不晚于 :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => '早于',
                    'inverse' => '不早于',
                ],

                'summary' => [
                    'direct' => ':attribute 早于 :date',
                    'inverse' => ':attribute 不早于 :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => '等于日期',
                    'inverse' => '不等于日期',
                ],

                'summary' => [
                    'direct' => ':attribute 等于 :date',
                    'inverse' => ':attribute 不等于 :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => '等于月份',
                    'inverse' => '不等于月份',
                ],

                'summary' => [
                    'direct' => ':attribute 等于 :month',
                    'inverse' => ':attribute 不等于 :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => '等于年份',
                    'inverse' => '不等于年份',
                ],

                'summary' => [
                    'direct' => ':attribute 等于 :year',
                    'inverse' => ':attribute 不等于 :year',
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
                    'direct' => '等于',
                    'inverse' => '不等于',
                ],

                'summary' => [
                    'direct' => ':attribute 等于 :number',
                    'inverse' => ':attribute 不等于 :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => '最大为',
                    'inverse' => '大于',
                ],

                'summary' => [
                    'direct' => ':attribute 最大为 :number',
                    'inverse' => ':attribute 大于 :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => '最小为',
                    'inverse' => '小于',
                ],

                'summary' => [
                    'direct' => ':attribute 最小为 :number',
                    'inverse' => ':attribute 小于 :number',
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
                    'label' => '求和',
                    'summary' => ':attribute 总和',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => '聚合函数',
                ],

                'number' => [
                    'label' => '数值',
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
                    'direct' => '包含 :count 个 :relationship',
                    'inverse' => '不包含 :count 个 :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => '最多包含',
                    'inverse' => '超过',
                ],

                'summary' => [
                    'direct' => '最多包含 :count 个 :relationship',
                    'inverse' => '超过 :count 个 :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => '至少包含',
                    'inverse' => '少于',
                ],

                'summary' => [
                    'direct' => '至少包含 :count 个 :relationship',
                    'inverse' => '少于 :count 个 :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => '为空',
                    'inverse' => '不为空',
                ],

                'summary' => [
                    'direct' => ':relationship 为空',
                    'inverse' => ':relationship 不为空',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => '等于',
                        'inverse' => '不等于',
                    ],

                    'multiple' => [
                        'direct' => '包含',
                        'inverse' => '不包含',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship 等于 :values',
                        'inverse' => ':relationship 不等于 :values',
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
                        'label' => '值列表',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => '数量',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => '等于',
                    'inverse' => '不等于',
                ],

                'summary' => [
                    'direct' => ':attribute 等于 :values',
                    'inverse' => ':attribute 不等于 :values',
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
                        'label' => '值列表',
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
                    'direct' => '以...结尾',
                    'inverse' => '不以...结尾',
                ],

                'summary' => [
                    'direct' => ':attribute 以 ":text" 结尾',
                    'inverse' => ':attribute 不以 ":text" 结尾',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => '等于',
                    'inverse' => '不等于',
                ],

                'summary' => [
                    'direct' => ':attribute 等于 ":text"',
                    'inverse' => ':attribute 不等于 ":text"',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => '以...开头',
                    'inverse' => '不以...开头',
                ],

                'summary' => [
                    'direct' => ':attribute 以 ":text" 开头',
                    'inverse' => ':attribute 不以 ":text" 开头',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => '文本',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => '添加规则',
        ],

        'add_rule_group' => [
            'label' => '添加规则组',
        ],

    ],

];
