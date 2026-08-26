<?php

return [

    'label' => 'Kijenzi cha maswali',

    'form' => [
        'operator' => [
            'label' => 'Opereta',
        ],

        'or_groups' => [
            'label' => 'Makundi',

            'group' => [
                'label' => 'Kundi',
            ],

            'block' => [
                'label' => 'Sharti la OR',
                'or' => 'OR',
            ],
        ],

        'rules' => [
            'label' => 'Sheria',

            'item' => [
                'and' => 'AND',
            ],
        ],
    ],

    'no_rules' => '(Hakuna sheria)',

    'max_rules_reached_tooltip' => 'Umefikia kikomo cha sheria :count.',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [
        'is_filled' => [
            'label' => [
                'direct' => 'Imejazwa',
                'inverse' => 'Ni tupu',
            ],

            'summary' => [
                'direct' => ':attribute imejazwa',
                'inverse' => ':attribute ni tupu',
            ],
        ],

        'boolean' => [
            'is_true' => [
                'label' => [
                    'direct' => 'Ni kweli',
                    'inverse' => 'Si kweli',
                ],

                'summary' => [
                    'direct' => ':attribute ni kweli',
                    'inverse' => ':attribute si kweli',
                ],
            ],
        ],

        'date' => [
            'is_after' => [
                'label' => [
                    'direct' => 'Ni baada ya',
                    'inverse' => 'Si baada ya',
                ],

                'summary' => [
                    'direct' => ':attribute ni baada ya :date',
                    'inverse' => ':attribute si baada ya :date',
                ],
            ],

            'is_before' => [
                'label' => [
                    'direct' => 'Ni kabla ya',
                    'inverse' => 'Si kabla ya',
                ],

                'summary' => [
                    'direct' => ':attribute ni kabla ya :date',
                    'inverse' => ':attribute si kabla ya :date',
                ],
            ],

            'is_date' => [
                'label' => [
                    'direct' => 'Ni tarehe',
                    'inverse' => 'Si tarehe',
                ],

                'summary' => [
                    'direct' => ':attribute ni :date',
                    'inverse' => ':attribute si :date',
                ],
            ],

            'is_month' => [
                'label' => [
                    'direct' => 'Ni mwezi',
                    'inverse' => 'Si mwezi',
                ],

                'summary' => [
                    'direct' => ':attribute ni :month',
                    'inverse' => ':attribute si :month',
                ],
            ],

            'is_year' => [
                'label' => [
                    'direct' => 'Ni mwaka',
                    'inverse' => 'Si mwaka',
                ],

                'summary' => [
                    'direct' => ':attribute ni :year',
                    'inverse' => ':attribute si :year',
                ],
            ],

            'unit_labels' => [
                'second' => 'Sekunde',
                'minute' => 'Dakika',
                'hour' => 'Saa',
                'day' => 'Siku',
                'week' => 'Wiki',
                'month' => 'Miezi',
                'quarter' => 'Robo',
                'year' => 'Miaka',
            ],

            'presets' => [
                'past_decade' => 'Muongo uliopita',
                'past_5_years' => 'Miaka 5 iliyopita',
                'past_2_years' => 'Miaka 2 iliyopita',
                'past_year' => 'Mwaka uliopita',
                'past_6_months' => 'Miezi 6 iliyopita',
                'past_quarter' => 'Robo iliyopita',
                'past_month' => 'Mwezi uliopita',
                'past_2_weeks' => 'Wiki 2 zilizopita',
                'past_week' => 'Wiki iliyopita',
                'past_hour' => 'Saa iliyopita',
                'past_minute' => 'Dakika iliyopita',
                'this_decade' => 'Muongo huu',
                'this_year' => 'Mwaka huu',
                'this_quarter' => 'Robo hii',
                'this_month' => 'Mwezi huu',
                'today' => 'Leo',
                'this_hour' => 'Saa hii',
                'this_minute' => 'Dakika hii',
                'next_minute' => 'Dakika ijayo',
                'next_hour' => 'Saa ijayo',
                'next_week' => 'Wiki ijayo',
                'next_2_weeks' => 'Wiki 2 zijazo',
                'next_month' => 'Mwezi ujao',
                'next_quarter' => 'Robo ijayo',
                'next_6_months' => 'Miezi 6 ijayo',
                'next_year' => 'Mwaka ujao',
                'next_2_years' => 'Miaka 2 ijayo',
                'next_5_years' => 'Miaka 5 ijayo',
                'next_decade' => 'Muongo ujao',
                'custom' => 'Maalum',
            ],

            'form' => [
                'date' => [
                    'label' => 'Tarehe',
                ],

                'month' => [
                    'label' => 'Mwezi',
                ],

                'year' => [
                    'label' => 'Mwaka',
                ],

                'mode' => [
                    'label' => 'Aina ya tarehe',

                    'options' => [
                        'absolute' => 'Tarehe maalum',
                        'relative' => 'Kipindi kinachohama',
                    ],
                ],

                'preset' => [
                    'label' => 'Kipindi',
                ],

                'relative_value' => [
                    'label' => 'Ngapi',
                ],

                'relative_unit' => [
                    'label' => 'Kitengo cha muda',
                ],

                'tense' => [
                    'label' => 'Muda',

                    'options' => [
                        'past' => 'Iliyopita',
                        'future' => 'Ijayo',
                    ],
                ],
            ],
        ],

        'number' => [
            'equals' => [
                'label' => [
                    'direct' => 'Sawa na',
                    'inverse' => 'Si sawa na',
                ],

                'summary' => [
                    'direct' => ':attribute ni sawa na :number',
                    'inverse' => ':attribute si sawa na :number',
                ],
            ],

            'is_max' => [
                'label' => [
                    'direct' => 'Ni kikomo cha juu',
                    'inverse' => 'Ni kubwa kuliko',
                ],

                'summary' => [
                    'direct' => ':attribute ni kikomo cha juu cha :number',
                    'inverse' => ':attribute ni kubwa kuliko :number',
                ],
            ],

            'is_min' => [
                'label' => [
                    'direct' => 'Ni kikomo cha chini',
                    'inverse' => 'Ni ndogo kuliko',
                ],

                'summary' => [
                    'direct' => ':attribute ni kikomo cha chini cha :number',
                    'inverse' => ':attribute ni ndogo kuliko :number',
                ],
            ],

            'aggregates' => [
                'average' => [
                    'label' => 'Wastani',
                    'summary' => 'Wastani wa :attribute',
                ],

                'max' => [
                    'label' => 'Max',
                    'summary' => 'Max ya :attribute',
                ],

                'min' => [
                    'label' => 'Min',
                    'summary' => 'Min ya :attribute',
                ],

                'sum' => [
                    'label' => 'Jumla',
                    'summary' => 'Jumla ya :attribute',
                ],
            ],

            'form' => [
                'aggregate' => [
                    'label' => 'Jumuisho',
                ],

                'number' => [
                    'label' => 'Namba',
                ],
            ],
        ],

        'relationship' => [
            'equals' => [
                'label' => [
                    'direct' => 'Ina',
                    'inverse' => 'Haina',
                ],

                'summary' => [
                    'direct' => 'Ina :count :relationship',
                    'inverse' => 'Haina :count :relationship',
                ],
            ],

            'has_max' => [
                'label' => [
                    'direct' => 'Ina kikomo cha juu',
                    'inverse' => 'Ina zaidi ya',
                ],

                'summary' => [
                    'direct' => 'Ina kikomo cha juu cha :count :relationship',
                    'inverse' => 'Ina zaidi ya :count :relationship',
                ],
            ],

            'has_min' => [
                'label' => [
                    'direct' => 'Ina kikomo cha chini',
                    'inverse' => 'Ina chini ya',
                ],

                'summary' => [
                    'direct' => 'Ina kikomo cha chini cha :count :relationship',
                    'inverse' => 'Ina chini ya :count :relationship',
                ],
            ],

            'is_empty' => [
                'label' => [
                    'direct' => 'Ni tupu',
                    'inverse' => 'Si tupu',
                ],

                'summary' => [
                    'direct' => ':relationship ni tupu',
                    'inverse' => ':relationship si tupu',
                ],
            ],

            'is_related_to' => [
                'label' => [
                    'single' => [
                        'direct' => 'Ni',
                        'inverse' => 'Si',
                    ],

                    'multiple' => [
                        'direct' => 'Ina',
                        'inverse' => 'Haina',
                    ],
                ],

                'summary' => [
                    'single' => [
                        'direct' => ':relationship ni :values',
                        'inverse' => ':relationship si :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship ina :values',
                        'inverse' => ':relationship haina :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' au ',
                    ],
                ],

                'form' => [
                    'value' => [
                        'label' => 'Thamani',
                    ],

                    'values' => [
                        'label' => 'Thamani',
                    ],
                ],
            ],

            'form' => [
                'count' => [
                    'label' => 'Idadi',
                ],
            ],
        ],

        'select' => [
            'is' => [
                'label' => [
                    'direct' => 'Ni',
                    'inverse' => 'Si',
                ],

                'summary' => [
                    'direct' => ':attribute ni :values',

                    'inverse' => ':attribute si :values',

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' au ',
                    ],
                ],

                'form' => [
                    'value' => [
                        'label' => 'Thamani',
                    ],

                    'values' => [
                        'label' => 'Thamani',
                    ],
                ],
            ],
        ],

        'text' => [
            'contains' => [
                'label' => [
                    'direct' => 'Ina',
                    'inverse' => 'Haina',
                ],

                'summary' => [
                    'direct' => ':attribute ina :text',
                    'inverse' => ':attribute haina :text',
                ],
            ],

            'ends_with' => [
                'label' => [
                    'direct' => 'Huishia na',
                    'inverse' => 'Hauishi na',
                ],

                'summary' => [
                    'direct' => ':attribute huishia na :text',
                    'inverse' => ':attribute hauishi na :text',
                ],
            ],

            'equals' => [
                'label' => [
                    'direct' => 'Sawa na',
                    'inverse' => 'Si sawa na',
                ],

                'summary' => [
                    'direct' => ':attribute ni sawa na :text',
                    'inverse' => ':attribute si sawa na :text',
                ],
            ],

            'starts_with' => [
                'label' => [
                    'direct' => 'Huanza na',
                    'inverse' => 'Hakianzi na',
                ],

                'summary' => [
                    'direct' => ':attribute huanza na :text',
                    'inverse' => ':attribute hakianzi na :text',
                ],
            ],

            'form' => [
                'text' => [
                    'label' => 'Maandishi',
                ],
            ],
        ],
    ],

    'actions' => [
        'add_rule' => [
            'label' => 'Ongeza sheria',
        ],

        'add_rule_group' => [
            'label' => 'Ongeza OR',
        ],
    ],

];
