<?php

return [

    'label' => 'クエリビルダー',

    'form' => [

        'operator' => [
            'label' => 'オペレーター',
        ],

        'or_groups' => [

            'label' => 'グループ',

            'block' => [
                'label' => '論理和 (または)',
                'or' => 'または',
            ],

        ],

        'rules' => [

            'label' => 'ルール',

            'item' => [
                'and' => 'かつ',
            ],

        ],

    ],

    'no_rules' => '（ルールなし）',

    'item_separators' => [
        'and' => 'かつ',
        'or' => 'または',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => '入力あり',
                'inverse' => '空白',
            ],

            'summary' => [
                'direct' => ':attributeは入力されています',
                'inverse' => ':attribute は空白です',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => '真',
                    'inverse' => '偽',
                ],

                'summary' => [
                    'direct' => ':attributeは真です',
                    'inverse' => ':attribute は偽です',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => '以降',
                    'inverse' => '以降ではない',
                ],

                'summary' => [
                    'direct' => ':attributeは:date以降',
                    'inverse' => ':attributeは:date以降ではない',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => '以前',
                    'inverse' => '以前ではない',
                ],

                'summary' => [
                    'direct' => ':attributeは:date以前',
                    'inverse' => ':attributeは:date以前ではない',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => '日付である',
                    'inverse' => '日付でない',
                ],

                'summary' => [
                    'direct' => ':attributeは:dateである',
                    'inverse' => ':attributeは:dateではない',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => '月',
                    'inverse' => '月ではない',
                ],

                'summary' => [
                    'direct' => ':attributeは:monthである',
                    'inverse' => ':attributeは:monthではない',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => '年',
                    'inverse' => '年ではない',
                ],

                'summary' => [
                    'direct' => ':attributeは:yearである',
                    'inverse' => ':attributeは:yearではない',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => '日',
                ],

                'month' => [
                    'label' => '月',
                ],

                'year' => [
                    'label' => '年',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => '等しい',
                    'inverse' => '等しくない',
                ],

                'summary' => [
                    'direct' => ':attributeは:numberと等しい',
                    'inverse' => ':attributeは:numberと等しくない',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => '最大',
                    'inverse' => '以上',
                ],

                'summary' => [
                    'direct' => ':attributeは:number以下である',
                    'inverse' => 'attributeは:numberより大きい',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => '最小',
                    'inverse' => '以下',
                ],

                'summary' => [
                    'direct' => ':attributeは:number以上である',
                    'inverse' => ':attributeは:numberより小さい',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => '平均',
                    'summary' => ':attributeの平均',
                ],

                'max' => [
                    'label' => '最大値',
                    'summary' => ':attributeの最大値',
                ],

                'min' => [
                    'label' => '最小値',
                    'summary' => ':attributeの最小値',
                ],

                'sum' => [
                    'label' => '合計',
                    'summary' => ':attributeの合計',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => '集計',
                ],

                'number' => [
                    'label' => '数',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => '持っている',
                    'inverse' => '持っていない',
                ],

                'summary' => [
                    'direct' => ':count個の:relationshipを保持',
                    'inverse' => ':count個の:relationshipを非保持',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => '最大である',
                    'inverse' => 'より多い',
                ],

                'summary' => [
                    'direct' => ':count個以下の:relationshipを保持',
                    'inverse' => ':count個より多く:relationshipを保持',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => '最小である',
                    'inverse' => 'より少ない',
                ],

                'summary' => [
                    'direct' => ':count個以上の:relationshipを保持',
                    'inverse' => ':count個より少ない:relationshipを保持',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => '空である',
                    'inverse' => '空ではない',
                ],

                'summary' => [
                    'direct' => ':relationshipは空である',
                    'inverse' => ':relationshipは空ではない',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'である',
                        'inverse' => 'ではない',
                    ],

                    'multiple' => [
                        'direct' => '含む',
                        'inverse' => '含まない',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationshipは:valuesである',
                        'inverse' => ':relationshipは:valuesではない',
                    ],

                    'multiple' => [
                        'direct' => ':relationshipは:valuesを含む',
                        'inverse' => ':relationshipは:valuesを含まない',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' または ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => '値',
                    ],

                    'values' => [
                        'label' => '値',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => '数',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'である',
                    'inverse' => 'ではない',
                ],

                'summary' => [
                    'direct' => ':attributeは:valuesである',
                    'inverse' => ':attributeは:valuesではない',
                    'values_glue' => [
                        ', ',
                        'final' => ' または ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => '値',
                    ],

                    'values' => [
                        'label' => '値',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => '含む',
                    'inverse' => '含まない',
                ],

                'summary' => [
                    'direct' => ':attributeは:textを含む',
                    'inverse' => ':attributeは:textを含まない',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'で終わる',
                    'inverse' => 'で終わらない',
                ],

                'summary' => [
                    'direct' => ':attributeは:textで終わる',
                    'inverse' => ':attributeは:textで終わらない',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => '等しい',
                    'inverse' => '等しくない',
                ],

                'summary' => [
                    'direct' => ':attributeは:textと等しい',
                    'inverse' => ':attributeは:textと等しくない',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'で始まる',
                    'inverse' => 'で始まらない',
                ],

                'summary' => [
                    'direct' => ':attributeは:textで始まる',
                    'inverse' => ':attributeは:textで始まらない',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'テキスト',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'ルールを追加',
        ],

        'add_rule_group' => [
            'label' => 'ルールグループを追加',
        ],

    ],

];
