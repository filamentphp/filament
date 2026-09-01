<?php

return [
    'label' => 'Израда упита',

    'form' => [

        'operator' => [
            'label' => 'Оператор',
        ],

        'or_groups' => [

            'label' => 'Група',

            'block' => [
                'label' => 'Или (OR)',
                'or' => 'OR',
            ],

        ],

        'rules' => [

            'label' => 'Правила',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(Без правила)',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Има вредност',
                'inverse' => 'Нема вредност',
            ],

            'summary' => [
                'direct' => ':attribute има вредност',
                'inverse' => ':attribute нема вредност',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Исправо',
                    'inverse' => 'Није исправно',
                ],

                'summary' => [
                    'direct' => ':attribute је исправан',
                    'inverse' => ':attribute није исправан',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Долази после',
                    'inverse' => 'Не долази после',
                ],

                'summary' => [
                    'direct' => ':attribute долази после :date',
                    'inverse' => ':attribute не долази после :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Долази пре',
                    'inverse' => 'Не долази пре',
                ],

                'summary' => [
                    'direct' => ':attribute долази пре :date',
                    'inverse' => ':attribute не долази пре :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Датум',
                    'inverse' => 'Није датум',
                ],

                'summary' => [
                    'direct' => ':attribute је датум :date',
                    'inverse' => ':attribute није датум :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Месец',
                    'inverse' => 'Није месец',
                ],

                'summary' => [
                    'direct' => ':attribute је :month',
                    'inverse' => ':attribute није :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Година',
                    'inverse' => 'Није година',
                ],

                'summary' => [
                    'direct' => ':attribute је :year',
                    'inverse' => ':attribute није :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Датум',
                ],

                'month' => [
                    'label' => 'Месец',
                ],

                'year' => [
                    'label' => 'Година',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Једнако',
                    'inverse' => 'Неједнако',
                ],

                'summary' => [
                    'direct' => ':attribute је :number',
                    'inverse' => ':attribute није :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Највећи',
                    'inverse' => 'Већи је од',
                ],

                'summary' => [
                    'direct' => ':attribute је највећи :number',
                    'inverse' => ':attribute је већи од :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Најмањи',
                    'inverse' => 'Мањи је од',
                ],

                'summary' => [
                    'direct' => ':attribute је најмањи :number',
                    'inverse' => ':attribute је мањи од :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Просечан',
                    'summary' => 'Просек :attribute',
                ],

                'max' => [
                    'label' => 'Највећи',
                    'summary' => 'Највећи :attribute',
                ],

                'min' => [
                    'label' => 'Најмањи',
                    'summary' => 'Најмањи :attribute',
                ],

                'sum' => [
                    'label' => 'Збир',
                    'summary' => 'Sum of :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Агрегирано',
                ],

                'number' => [
                    'label' => 'Број',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Има',
                    'inverse' => 'Нема',
                ],

                'summary' => [
                    'direct' => 'Има :count :relationship',
                    'inverse' => 'Нема :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Има највише',
                    'inverse' => 'Има више од',
                ],

                'summary' => [
                    'direct' => 'Има највише :count :relationship',
                    'inverse' => 'Има више од :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Има најмање',
                    'inverse' => 'Има мање од',
                ],

                'summary' => [
                    'direct' => 'Има најмање :count :relationship',
                    'inverse' => 'Има мање од :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Не садржи податке',
                    'inverse' => 'Садржи податке',
                ],

                'summary' => [
                    'direct' => ':relationship је празно',
                    'inverse' => ':relationship није празно',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Јесте',
                        'inverse' => 'Није',
                    ],

                    'multiple' => [
                        'direct' => 'Садржи',
                        'inverse' => 'Не садржи',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship је :values',
                        'inverse' => ':relationship није :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship садржи :values',
                        'inverse' => ':relationship не садржи :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' или ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Вредност',
                    ],

                    'values' => [
                        'label' => 'Вредности',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Број',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Је',
                    'inverse' => 'Није',
                ],

                'summary' => [
                    'direct' => ':attribute је :values',
                    'inverse' => ':attribute није :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' или ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Вредност',
                    ],

                    'values' => [
                        'label' => 'Вредности',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Садржи',
                    'inverse' => 'Не садржи',
                ],

                'summary' => [
                    'direct' => ':attribute садржи :text',
                    'inverse' => ':attribute не садржи :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Завршава са',
                    'inverse' => 'Не завршава са',
                ],

                'summary' => [
                    'direct' => ':attribute завршава са :text',
                    'inverse' => ':attribute не завршава са :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Једнако',
                    'inverse' => 'Различито',
                ],

                'summary' => [
                    'direct' => ':attribute исто као :text',
                    'inverse' => ':attribute различито од :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Почиње са',
                    'inverse' => 'Не почиње са',
                ],

                'summary' => [
                    'direct' => ':attribute почиње са :text',
                    'inverse' => ':attribute не почиње са :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Текст',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Дода правило',
        ],

        'add_rule_group' => [
            'label' => 'Дода групу пра',
        ],

    ],

];
