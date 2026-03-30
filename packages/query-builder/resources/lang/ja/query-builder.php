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

            'unit_labels' => [
                'second' => '秒',
                'minute' => '分',
                'hour' => '時',
                'day' => '日',
                'week' => '週',
                'month' => '月',
                'quarter' => '四半期',
                'year' => '年',
            ],

            'presets' => [
                'past_decade' => '過去10年間',
                'past_5_years' => '過去5年間',
                'past_2_years' => '過去2年間',
                'past_year' => '過去1年間',
                'past_6_months' => '過去6ヶ月間',
                'past_quarter' => '過去3ヶ月間',
                'past_month' => '先月',
                'past_2_weeks' => '過去2週間',
                'past_week' => '先週',
                'past_hour' => '過去1時間',
                'past_minute' => '過去1分間',
                'this_decade' => 'この10年間',
                'this_year' => '今年',
                'this_quarter' => '今四半期',
                'this_month' => '今月',
                'today' => '今日',
                'this_hour' => 'この1時間',
                'this_minute' => 'この1分間',
                'next_minute' => '1分後',
                'next_hour' => '1時間後',
                'next_week' => '来週',
                'next_2_weeks' => '2週間後',
                'next_month' => '来月',
                'next_quarter' => '次四半期',
                'next_6_months' => '6ヶ月後',
                'next_year' => '来年',
                'next_2_years' => '2年後',
                'next_5_years' => '5年後',
                'next_decade' => '10年後',
                'custom' => 'カスタム',
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

                'mode' => [

                    'label' => '指定方法',

                    'options' => [
                        'absolute' => '日付指定',
                        'relative' => '期間指定',
                    ],

                ],

                'preset' => [
                    'label' => '期間',
                ],

                'relative_value' => [
                    'label' => '数値',
                ],

                'relative_unit' => [
                    'label' => '単位',
                ],

                'tense' => [

                    'label' => '時制',

                    'options' => [
                        'past' => '過去',
                        'future' => '今後',
                    ],

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
                    'direct' => '以下',
                    'inverse' => 'より大きい',
                ],

                'summary' => [
                    'direct' => ':attributeは:number以下',
                    'inverse' => ':attributeは:numberより大きい',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => '以上',
                    'inverse' => 'より小さい',
                ],

                'summary' => [
                    'direct' => ':attributeは:number以上',
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
