<?php

return [

    'label' => 'Созандаи дархостҳо',

    'form' => [

        'operator' => [
            'label' => 'Оператор',
        ],

        'or_groups' => [

            'label' => 'Гурӯҳҳо',

            'block' => [
                'label' => 'Дизюнксия (Ё)',
                'or' => 'Ё',
            ],

        ],

        'rules' => [

            'label' => 'Қоидаҳо',

            'item' => [
                'and' => 'ВА',
            ],

        ],

    ],

    'no_rules' => '(Қоидаҳо вуҷуд надоранд)',

    'item_separators' => [
        'and' => 'ВА',
        'or' => 'Ё',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Пур шудааст',
                'inverse' => 'Холӣ аст',
            ],

            'summary' => [
                'direct' => ':attribute пур шудааст',
                'inverse' => ':attribute холӣ аст',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Ҳа',
                    'inverse' => 'Не',
                ],

                'summary' => [
                    'direct' => ':attribute ҳақиқӣ аст',
                    'inverse' => ':attribute ҳақиқӣ нест',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Баъд аз',
                    'inverse' => 'Баъд аз нест',
                ],

                'summary' => [
                    'direct' => ':attribute баъд аз :date',
                    'inverse' => ':attribute баъд аз :date нест',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Пеш аз',
                    'inverse' => 'Пеш аз нест',
                ],

                'summary' => [
                    'direct' => ':attribute пеш аз :date',
                    'inverse' => ':attribute пеш аз :date нест',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Сана',
                    'inverse' => 'Сана нест',
                ],

                'summary' => [
                    'direct' => ':attribute = :date',
                    'inverse' => ':attribute ≠ :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Моҳ',
                    'inverse' => 'Моҳ нест',
                ],

                'summary' => [
                    'direct' => ':attribute = :month',
                    'inverse' => ':attribute ≠ :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Сол',
                    'inverse' => 'Сол нест',
                ],

                'summary' => [
                    'direct' => ':attribute = :year',
                    'inverse' => ':attribute ≠ :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Сония',
                'minute' => 'Дақиқа',
                'hour' => 'Соат',
                'day' => 'Рӯз',
                'week' => 'Ҳафта',
                'month' => 'Моҳ',
                'quarter' => 'Семоҳа',
                'year' => 'Сол',
            ],

            'presets' => [
                'past_decade' => 'Даҳсолаи гузашта',
                'past_5_years' => '5 соли гузашта',
                'past_2_years' => '2 соли гузашта',
                'past_year' => 'Соли гузашта',
                'past_6_months' => '6 моҳи гузашта',
                'past_quarter' => 'Семоҳаи гузашта',
                'past_month' => 'Моҳи гузашта',
                'past_2_weeks' => '2 ҳафтаи гузашта',
                'past_week' => 'Ҳафтаи гузашта',
                'past_hour' => 'Соати гузашта',
                'past_minute' => 'Дақиқаи гузашта',
                'this_decade' => 'Ин даҳсола',
                'this_year' => 'Имсол',
                'this_quarter' => 'Ин семоҳа',
                'this_month' => 'Ин моҳ',
                'today' => 'Имрӯз',
                'this_hour' => 'Ин соат',
                'this_minute' => 'Ин дақиқа',
                'next_minute' => 'Дақиқаи оянда',
                'next_hour' => 'Соати оянда',
                'next_week' => 'Ҳафтаи оянда',
                'next_2_weeks' => '2 ҳафтаи оянда',
                'next_month' => 'Моҳи оянда',
                'next_quarter' => 'Семоҳаи оянда',
                'next_6_months' => '6 моҳи оянда',
                'next_year' => 'Соли оянда',
                'next_2_years' => '2 соли оянда',
                'next_5_years' => '5 соли оянда',
                'next_decade' => 'Даҳсолаи оянда',
                'custom' => 'Диапазони фармоишӣ',
            ],

            'form' => [

                'date' => [
                    'label' => 'Сана',
                ],

                'month' => [
                    'label' => 'Моҳ',
                ],

                'year' => [
                    'label' => 'Сол',
                ],

                'mode' => [

                    'label' => 'Навъи сана',

                    'options' => [
                        'absolute' => 'Санаи мушаххас',
                        'relative' => 'Диапазони нисбӣ',
                    ],

                ],

                'preset' => [
                    'label' => 'Давра',
                ],

                'relative_value' => [
                    'label' => 'Миқдор',
                ],

                'relative_unit' => [
                    'label' => 'Воҳиди вақт',
                ],

                'tense' => [

                    'label' => 'Самт',

                    'options' => [
                        'past' => 'Гузашта',
                        'future' => 'Оянда',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Баробар аст',
                    'inverse' => 'Баробар нест',
                ],

                'summary' => [
                    'direct' => ':attribute = :number',
                    'inverse' => ':attribute ≠ :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Ҳадди аксар',
                    'inverse' => 'Зиёда аз',
                ],

                'summary' => [
                    'direct' => ':attribute ≤ :number',
                    'inverse' => ':attribute > :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Ҳадди ақал',
                    'inverse' => 'Камтар аз',
                ],

                'summary' => [
                    'direct' => ':attribute ≥ :number',
                    'inverse' => ':attribute < :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Миёна',
                    'summary' => 'Миёнаи :attribute',
                ],

                'max' => [
                    'label' => 'Макс',
                    'summary' => 'Макс :attribute',
                ],

                'min' => [
                    'label' => 'Мин',
                    'summary' => 'Мин :attribute',
                ],

                'sum' => [
                    'label' => 'Ҷамъ',
                    'summary' => 'Ҷамъи :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Хулоса',
                ],

                'number' => [
                    'label' => 'Рақам',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Дорад',
                    'inverse' => 'Надорад',
                ],

                'summary' => [
                    'direct' => 'Дорад :count :relationship',
                    'inverse' => 'Надорад :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Ҳадди аксар дорад',
                    'inverse' => 'Бештар аз дорад',
                ],

                'summary' => [
                    'direct' => 'Ҳадди аксар :count :relationship дорад',
                    'inverse' => 'Бештар аз :count :relationship дорад',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Ҳадди ақал дорад',
                    'inverse' => 'Камтар аз дорад',
                ],

                'summary' => [
                    'direct' => 'Ҳадди ақал :count :relationship дорад',
                    'inverse' => 'Камтар аз :count :relationship дорад',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Холӣ аст',
                    'inverse' => 'Холӣ нест',
                ],

                'summary' => [
                    'direct' => ':relationship холӣ аст',
                    'inverse' => ':relationship холӣ нест',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Мебошад',
                        'inverse' => 'Намебошад',
                    ],

                    'multiple' => [
                        'direct' => 'Дар бар мегирад',
                        'inverse' => 'Дар бар намегирад',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship = :values',
                        'inverse' => ':relationship ≠ :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship дорои :values мебошад',
                        'inverse' => ':relationship дорои :values нест',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' ё ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Қимат',
                    ],

                    'values' => [
                        'label' => 'Қиматҳо',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Миқдор',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Мебошад',
                    'inverse' => 'Намебошад',
                ],

                'summary' => [
                    'direct' => ':attribute = :values',
                    'inverse' => ':attribute ≠ :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' ё ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Қимат',
                    ],

                    'values' => [
                        'label' => 'Қиматҳо',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Дар бар мегирад',
                    'inverse' => 'Дар бар намегирад',
                ],

                'summary' => [
                    'direct' => ':attribute дорои :text мебошад',
                    'inverse' => ':attribute дорои :text нест',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Бо анҷом меёбад',
                    'inverse' => 'Бо анҷом намеёбад',
                ],

                'summary' => [
                    'direct' => ':attribute бо :text анҷом меёбад',
                    'inverse' => ':attribute бо :text анҷом намеёбад',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Баробар аст',
                    'inverse' => 'Баробар нест',
                ],

                'summary' => [
                    'direct' => ':attribute = :text',
                    'inverse' => ':attribute ≠ :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Бо оғоз мешавад',
                    'inverse' => 'Бо оғоз намешавад',
                ],

                'summary' => [
                    'direct' => ':attribute бо :text оғоз мешавад',
                    'inverse' => ':attribute бо :text оғоз намешавад',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Матн',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Илова кардани қоида',
        ],

        'add_rule_group' => [
            'label' => 'Илова кардани гурӯҳи қоидаҳо',
        ],

    ],

];
