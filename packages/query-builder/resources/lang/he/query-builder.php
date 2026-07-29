<?php

return [

    'label' => 'בונה שאילתות',

    'form' => [

        'operator' => [
            'label' => 'תנאי',
        ],

        'or_groups' => [

            'label' => 'קבוצות',

            'group' => [
                'label' => 'קבוצה',
            ],

            'block' => [
                'label' => 'או (OR)',
                'or' => 'או',
            ],

        ],

        'rules' => [

            'label' => 'כללים',

            'item' => [
                'and' => 'וגם',
            ],

        ],

    ],

    'no_rules' => '(אין כללים)',

    'max_rules_reached_tooltip' => 'הגעת למספר המרבי של :count כללים.',

    'item_separators' => [
        'and' => 'וגם',
        'or' => 'או',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'מלא',
                'inverse' => 'ריק',
            ],

            'summary' => [
                'direct' => ':attribute מלא',
                'inverse' => ':attribute ריק',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'כן',
                    'inverse' => 'לא',
                ],

                'summary' => [
                    'direct' => ':attribute הוא כן',
                    'inverse' => ':attribute הוא לא',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'אחרי תאריך',
                    'inverse' => 'לא אחרי תאריך',
                ],

                'summary' => [
                    'direct' => ':attribute אחרי :date',
                    'inverse' => ':attribute לא אחרי :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'לפני תאריך',
                    'inverse' => 'לא לפני תאריך',
                ],

                'summary' => [
                    'direct' => ':attribute לפני :date',
                    'inverse' => ':attribute לא לפני :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'תאריך זהה',
                    'inverse' => 'תאריך שונה',
                ],

                'summary' => [
                    'direct' => ':attribute תאריך זהה ל-:date',
                    'inverse' => ':attribute שונה מ-:date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'חודש זהה',
                    'inverse' => 'חודש שונה',
                ],

                'summary' => [
                    'direct' => ':attribute זהה לחודש :month',
                    'inverse' => ':attribute שונה מחודש :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'שנה זהה',
                    'inverse' => 'שנה שונה',
                ],

                'summary' => [
                    'direct' => ':attribute זהה לשנה :year',
                    'inverse' => ':attribute שונה משנה :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'שניות',
                'minute' => 'דקות',
                'hour' => 'שעות',
                'day' => 'ימים',
                'week' => 'שבועות',
                'month' => 'חודשים',
                'quarter' => 'רבעונים',
                'year' => 'שנים',
            ],

            'presets' => [
                'past_decade' => 'העשור האחרון',
                'past_5_years' => '5 השנים האחרונות',
                'past_2_years' => 'השנתיים האחרונות',
                'past_year' => 'השנה האחרונה',
                'past_6_months' => '6 החודשים האחרונים',
                'past_quarter' => 'הרבעון האחרון',
                'past_month' => 'החודש האחרון',
                'past_2_weeks' => 'השבועיים האחרונים',
                'past_week' => 'השבוע האחרון',
                'past_hour' => 'השעה האחרונה',
                'past_minute' => 'הדקה האחרונה',
                'this_decade' => 'העשור הנוכחי',
                'this_year' => 'השנה הנוכחית',
                'this_quarter' => 'הרבעון הנוכחי',
                'this_month' => 'החודש הנוכחי',
                'today' => 'היום',
                'this_hour' => 'השעה הנוכחית',
                'this_minute' => 'הדקה הנוכחית',
                'next_minute' => 'הדקה הבאה',
                'next_hour' => 'השעה הבאה',
                'next_week' => 'השבוע הבא',
                'next_2_weeks' => 'השבועיים הבאים',
                'next_month' => 'החודש הבא',
                'next_quarter' => 'הרבעון הבא',
                'next_6_months' => '6 החודשים הבאים',
                'next_year' => 'השנה הבאה',
                'next_2_years' => 'השנתיים הבאות',
                'next_5_years' => '5 השנים הבאות',
                'next_decade' => 'העשור הבא',
                'custom' => 'מותאם אישית',
            ],

            'form' => [

                'date' => [
                    'label' => 'תאריך',
                ],

                'month' => [
                    'label' => 'חודש',
                ],

                'year' => [
                    'label' => 'שנה',
                ],

                'mode' => [

                    'label' => 'סוג תאריך',

                    'options' => [
                        'absolute' => 'תאריך מסוים',
                        'relative' => 'חלון מתגלגל',
                    ],

                ],

                'preset' => [
                    'label' => 'טווח זמן',
                ],

                'relative_value' => [
                    'label' => 'כמה',
                ],

                'relative_unit' => [
                    'label' => 'יחידת זמן',
                ],

                'tense' => [

                    'label' => 'עבר או עתיד',

                    'options' => [
                        'past' => 'עבר',
                        'future' => 'עתיד',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'שווה ל',
                    'inverse' => 'שונה מ',
                ],

                'summary' => [
                    'direct' => ':attribute שווה ל-:number',
                    'inverse' => ':attribute שונה מ-:number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'לא יותר מ',
                    'inverse' => 'גדול מ',
                ],

                'summary' => [
                    'direct' => ':attribute לא יותר מ-:number',
                    'inverse' => ':attribute גדול מ-:number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'גדול או שווה ל',
                    'inverse' => 'קטן מ',
                ],

                'summary' => [
                    'direct' => ':attribute גדול או שווה ל-:number',
                    'inverse' => ':attribute קטן מ-:number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'ממוצע',
                    'summary' => 'ממוצע של :attribute',
                ],

                'max' => [
                    'label' => 'הערך הגבוה ביותר',
                    'summary' => 'מקסימום של :attribute',
                ],

                'min' => [
                    'label' => 'הערך הנמוך ביותר',
                    'summary' => 'מינימום של :attribute',
                ],

                'sum' => [
                    'label' => 'סכום כולל',
                    'summary' => 'סכום של :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'סוג חישוב',
                ],

                'number' => [
                    'label' => 'מספר',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'יש',
                    'inverse' => 'אין',
                ],

                'summary' => [
                    'direct' => 'יש :count :relationship',
                    'inverse' => 'אין :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'עד מקסימום',
                    'inverse' => 'יותר מ',
                ],

                'summary' => [
                    'direct' => 'יש עד :count :relationship',
                    'inverse' => 'יש יותר מ-:count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'לפחות',
                    'inverse' => 'פחות מ',
                ],

                'summary' => [
                    'direct' => 'יש לפחות :count :relationship',
                    'inverse' => 'יש פחות מ-:count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'ריק',
                    'inverse' => 'לא ריק',
                ],

                'summary' => [
                    'direct' => ':relationship ריק',
                    'inverse' => ':relationship לא ריק',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'הוא',
                        'inverse' => 'אינו',
                    ],

                    'multiple' => [
                        'direct' => 'מכיל',
                        'inverse' => 'לא מכיל',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship הוא :values',
                        'inverse' => ':relationship אינו :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship מכיל :values',
                        'inverse' => ':relationship לא מכיל :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' או ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'ערך',
                    ],

                    'values' => [
                        'label' => 'ערכים',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'כמות',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'הוא',
                    'inverse' => 'אינו',
                ],

                'summary' => [
                    'direct' => ':attribute הוא :values',
                    'inverse' => ':attribute אינו :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' או ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'ערך',
                    ],

                    'values' => [
                        'label' => 'ערכים',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'מכיל',
                    'inverse' => 'לא מכיל',
                ],

                'summary' => [
                    'direct' => ':attribute מכיל :text',
                    'inverse' => ':attribute לא מכיל :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'מסתיים ב',
                    'inverse' => 'לא מסתיים ב',
                ],

                'summary' => [
                    'direct' => ':attribute מסתיים ב-:text',
                    'inverse' => ':attribute לא מסתיים ב-:text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'שווה ל',
                    'inverse' => 'שונה מ',
                ],

                'summary' => [
                    'direct' => ':attribute שווה ל-:text',
                    'inverse' => ':attribute שונה מ-:text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'מתחיל ב',
                    'inverse' => 'לא מתחיל ב',
                ],

                'summary' => [
                    'direct' => ':attribute מתחיל ב-:text',
                    'inverse' => ':attribute לא מתחיל ב-:text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'טקסט',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'הוסף כלל',
        ],

        'add_rule_group' => [
            'label' => 'הוסף קבוצת כללים',
        ],

    ],

];
