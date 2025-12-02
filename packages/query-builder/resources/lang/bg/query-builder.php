<?php

return [

    'label' => 'Генератор на заявки',

    'form' => [

        'operator' => [
            'label' => 'Оператор',
        ],

        'or_groups' => [

            'label' => 'Групи',

            'block' => [
                'label' => 'Дизюнкция (ИЛИ)',
                'or' => 'ИЛИ',
            ],

        ],

        'rules' => [

            'label' => 'Правила',

            'item' => [
                'and' => 'И',
            ],

        ],

    ],

    'no_rules' => '(няма правила)',

    'item_separators' => [
        'and' => 'И',
        'or' => 'ИЛИ',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Е попълнено',
                'inverse' => 'Е празно',
            ],

            'summary' => [
                'direct' => ':attribute е попълнен',
                'inverse' => ':attribute е празен',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Е истина',
                    'inverse' => 'Не е истина',
                ],

                'summary' => [
                    'direct' => ':attribute е истина',
                    'inverse' => ':attribute не е истина',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'E след',
                    'inverse' => 'Не е след',
                ],

                'summary' => [
                    'direct' => ':attribute е след :date',
                    'inverse' => ':attribute не е след :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Е преди',
                    'inverse' => 'Не е преди',
                ],

                'summary' => [
                    'direct' => ':attribute е преди :date',
                    'inverse' => ':attribute не е преди :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Е дата',
                    'inverse' => 'Не е дата',
                ],

                'summary' => [
                    'direct' => ':attribute е :date',
                    'inverse' => ':attribute не е :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Е месец',
                    'inverse' => 'Не е месец',
                ],

                'summary' => [
                    'direct' => ':attribute е :month',
                    'inverse' => ':attribute не е :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Е година',
                    'inverse' => 'Не е година',
                ],

                'summary' => [
                    'direct' => ':attribute е :year',
                    'inverse' => ':attribute не е :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Дата',
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
                    'direct' => 'Е равно на',
                    'inverse' => 'Не е равно на',
                ],

                'summary' => [
                    'direct' => ':attribute е равно на :number',
                    'inverse' => ':attribute не е равно на :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Е максимум',
                    'inverse' => 'Е по-голямо от',
                ],

                'summary' => [
                    'direct' => ':attribute е максимум :number',
                    'inverse' => ':attribute е по-голямо от :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Е минимум',
                    'inverse' => 'Е по-малко от',
                ],

                'summary' => [
                    'direct' => ':attribute е минимум :number',
                    'inverse' => ':attribute е по-малко от :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Средно',
                    'summary' => 'Средно на :attribute',
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
                    'label' => 'Сума',
                    'summary' => 'Сума на :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Агрегат',
                ],

                'number' => [
                    'label' => 'Число',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Има',
                    'inverse' => 'Няма',
                ],

                'summary' => [
                    'direct' => 'Има :count :relationship',
                    'inverse' => 'Няма :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Има максимум',
                    'inverse' => 'Има повече от',
                ],

                'summary' => [
                    'direct' => 'Има максимум :count :relationship',
                    'inverse' => 'Има повече от :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Има минимум',
                    'inverse' => 'Има по-малко от',
                ],

                'summary' => [
                    'direct' => 'Има минимум :count :relationship',
                    'inverse' => 'Има по-малко от :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Е празно',
                    'inverse' => 'Не е празно',
                ],

                'summary' => [
                    'direct' => ':relationship е празно',
                    'inverse' => ':relationship не е празно',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Е',
                        'inverse' => 'Не е',
                    ],

                    'multiple' => [
                        'direct' => 'Съдържа',
                        'inverse' => 'Не съдържа',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship е :values',
                        'inverse' => ':relationship не е :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship съдържа :values',
                        'inverse' => ':relationship не съдържа :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' или ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Стойност',
                    ],

                    'values' => [
                        'label' => 'Стойности',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Брой',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Е',
                    'inverse' => 'Не е',
                ],

                'summary' => [
                    'direct' => ':attribute е :values',
                    'inverse' => ':attribute не е :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' или ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Стойност',
                    ],

                    'values' => [
                        'label' => 'Стойности',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Съдържа',
                    'inverse' => 'Не съдържа',
                ],

                'summary' => [
                    'direct' => ':attribute съдържа :text',
                    'inverse' => ':attribute не съдържа :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Завършва на',
                    'inverse' => 'Не завършва на',
                ],

                'summary' => [
                    'direct' => ':attribute завършва на :text',
                    'inverse' => ':attribute не завършва на :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Е равно на',
                    'inverse' => 'Не е равно на',
                ],

                'summary' => [
                    'direct' => ':attribute е равно на :text',
                    'inverse' => ':attribute не е равно на :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Започва с',
                    'inverse' => 'Не започва с',
                ],

                'summary' => [
                    'direct' => ':attribute започва с :text',
                    'inverse' => ':attribute не започва с :text',
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
            'label' => 'Добави правило',
        ],

        'add_rule_group' => [
            'label' => 'Добави група',
        ],

    ],

];
