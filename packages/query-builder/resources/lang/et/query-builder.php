<?php

return [

    'label' => 'Päringu koostaja',

    'form' => [

        'operator' => [
            'label' => 'Operaator',
        ],

        'or_groups' => [

            'label' => 'Rühmad',

            'block' => [
                'label' => 'VÕI tingimus',
                'or' => 'VÕI',
            ],

        ],

        'rules' => [

            'label' => 'Reeglid',

            'item' => [
                'and' => 'JA',
            ],

        ],

    ],

    'no_rules' => '(Reegleid pole)',

    'item_separators' => [
        'and' => 'JA',
        'or' => 'VÕI',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'On täidetud',
                'inverse' => 'On tühi',
            ],

            'summary' => [
                'direct' => ':attribute on täidetud',
                'inverse' => ':attribute on tühi',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'On tõene',
                    'inverse' => 'On väär',
                ],

                'summary' => [
                    'direct' => ':attribute on tõene',
                    'inverse' => ':attribute on väär',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'On pärast',
                    'inverse' => 'Ei ole pärast',
                ],

                'summary' => [
                    'direct' => ':attribute on pärast :date',
                    'inverse' => ':attribute ei ole pärast :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'On enne',
                    'inverse' => 'Ei ole enne',
                ],

                'summary' => [
                    'direct' => ':attribute on enne :date',
                    'inverse' => ':attribute ei ole enne :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'On kuupäev',
                    'inverse' => 'Ei ole kuupäev',
                ],

                'summary' => [
                    'direct' => ':attribute on :date',
                    'inverse' => ':attribute ei ole :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'On kuu',
                    'inverse' => 'Ei ole kuu',
                ],

                'summary' => [
                    'direct' => ':attribute on :month',
                    'inverse' => ':attribute ei ole :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'On aasta',
                    'inverse' => 'Ei ole aasta',
                ],

                'summary' => [
                    'direct' => ':attribute on :year',
                    'inverse' => ':attribute ei ole :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Sekundid',
                'minute' => 'Minutid',
                'hour' => 'Tunnid',
                'day' => 'Päevad',
                'week' => 'Nädalad',
                'month' => 'Kuud',
                'quarter' => 'Kvartalid',
                'year' => 'Aastad',
            ],

            'presets' => [
                'past_decade' => 'Viimane kümnend',
                'past_5_years' => 'Viimased 5 aastat',
                'past_2_years' => 'Viimased 2 aastat',
                'past_year' => 'Viimane aasta',
                'past_6_months' => 'Viimased 6 kuud',
                'past_quarter' => 'Viimane kvartal',
                'past_month' => 'Viimane kuu',
                'past_2_weeks' => 'Viimased 2 nädalat',
                'past_week' => 'Viimane nädal',
                'past_hour' => 'Viimane tund',
                'past_minute' => 'Viimane minut',
                'this_decade' => 'See kümnend',
                'this_year' => 'See aasta',
                'this_quarter' => 'See kvartal',
                'this_month' => 'See kuu',
                'today' => 'Täna',
                'this_hour' => 'See tund',
                'this_minute' => 'See minut',
                'next_minute' => 'Järgmine minut',
                'next_hour' => 'Järgmine tund',
                'next_week' => 'Järgmine nädal',
                'next_2_weeks' => 'Järgmised 2 nädalat',
                'next_month' => 'Järgmine kuu',
                'next_quarter' => 'Järgmine kvartal',
                'next_6_months' => 'Järgmised 6 kuud',
                'next_year' => 'Järgmine aasta',
                'next_2_years' => 'Järgmised 2 aastat',
                'next_5_years' => 'Järgmised 5 aastat',
                'next_decade' => 'Järgmine kümnend',
                'custom' => 'Kohandatud',
            ],

            'form' => [

                'date' => [
                    'label' => 'Kuupäev',
                ],

                'month' => [
                    'label' => 'Kuu',
                ],

                'year' => [
                    'label' => 'Aasta',
                ],

                'mode' => [

                    'label' => 'Kuupäeva tüüp',

                    'options' => [
                        'absolute' => 'Kindel kuupäev',
                        'relative' => 'Liikuv aken',
                    ],

                ],

                'preset' => [
                    'label' => 'Ajavahemik',
                ],

                'relative_value' => [
                    'label' => 'Kui palju',
                ],

                'relative_unit' => [
                    'label' => 'Ajaühik',
                ],

                'tense' => [

                    'label' => 'Ajavorm',

                    'options' => [
                        'past' => 'Minevik',
                        'future' => 'Tulevik',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Võrdub',
                    'inverse' => 'Ei võrdu',
                ],

                'summary' => [
                    'direct' => ':attribute võrdub :number',
                    'inverse' => ':attribute ei võrdu :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'On maksimaalne',
                    'inverse' => 'On suurem kui',
                ],

                'summary' => [
                    'direct' => ':attribute on vähemalt :number',
                    'inverse' => ':attribute on suurem kui :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'On vähemalt',
                    'inverse' => 'On väiksem kui',
                ],

                'summary' => [
                    'direct' => ':attribute on vähemalt :number',
                    'inverse' => ':attribute on väiksem kui :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Keskmine',
                    'summary' => 'Keskmine :attribute',
                ],

                'max' => [
                    'label' => 'Maks',
                    'summary' => 'Maks :attribute',
                ],

                'min' => [
                    'label' => 'Min',
                    'summary' => 'Min :attribute',
                ],

                'sum' => [
                    'label' => 'Summa',
                    'summary' => 'Summa :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Kogusumma',
                ],

                'number' => [
                    'label' => 'Number',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Omab',
                    'inverse' => 'Ei oma',
                ],

                'summary' => [
                    'direct' => 'Omab :count :relationship',
                    'inverse' => 'Ei oma :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Omab maksimaalselt',
                    'inverse' => 'Omab rohkem kui',
                ],

                'summary' => [
                    'direct' => 'Omab maksimaalselt :count :relationship',
                    'inverse' => 'Omab rohkem kui :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Omab minimaalselt',
                    'inverse' => 'Omab vähem kui',
                ],

                'summary' => [
                    'direct' => 'Omab minimaalselt :count :relationship',
                    'inverse' => 'Omab vähem kui :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Puudub',
                    'inverse' => 'Eksisteerib',
                ],

                'summary' => [
                    'direct' => ':relationship puudub',
                    'inverse' => ':relationship eksisteerib',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'On',
                        'inverse' => 'Ei ole',
                    ],

                    'multiple' => [
                        'direct' => 'Sisaldab',
                        'inverse' => 'Ei sisalda',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship on :values',
                        'inverse' => ':relationship ei ole :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship sisaldab :values',
                        'inverse' => ':relationship ei sisalda :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' või ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Väärtus',
                    ],

                    'values' => [
                        'label' => 'Väärtused',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Arv',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'On',
                    'inverse' => 'Ei ole',
                ],

                'summary' => [
                    'direct' => ':attribute on :values',
                    'inverse' => ':attribute ei ole :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' või ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Väärtus',
                    ],

                    'values' => [
                        'label' => 'Väärtused',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Sisaldab',
                    'inverse' => 'Ei sisalda',
                ],

                'summary' => [
                    'direct' => ':attribute sisaldab :text',
                    'inverse' => ':attribute ei sisalda :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Lõpeb',
                    'inverse' => 'Ei lõpe',
                ],

                'summary' => [
                    'direct' => ':attribute lõpeb :text',
                    'inverse' => ':attribute ei lõpe :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Võrdub',
                    'inverse' => 'Ei võrdu',
                ],

                'summary' => [
                    'direct' => ':attribute võrdub :text',
                    'inverse' => ':attribute ei võrdu :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Algab',
                    'inverse' => 'Ei alga',
                ],

                'summary' => [
                    'direct' => ':attribute algab :text',
                    'inverse' => ':attribute ei alga :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Tekst',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Lisa reegel',
        ],

        'add_rule_group' => [
            'label' => 'Lisa VÕI',
        ],

    ],

];
