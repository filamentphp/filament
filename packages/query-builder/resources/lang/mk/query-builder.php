<?php

return [

    'label' => 'Градител на барања',

    'form' => [

        'operator' => [
            'label' => 'Оператор',
        ],

        'or_groups' => [

            'label' => 'Групи',

            'block' => [
                'label' => 'ИЛИ услов',
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

    'no_rules' => '(Нема правила)',

    'item_separators' => [
        'and' => 'И',
        'or' => 'ИЛИ',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Е пополнета',
                'inverse' => 'Е празна',
            ],

            'summary' => [
                'direct' => ':attribute е пополнета',
                'inverse' => ':attribute е празна',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Е точно',
                    'inverse' => 'Е неточно',
                ],

                'summary' => [
                    'direct' => ':attribute е точно',
                    'inverse' => ':attribute е неточно',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Е после',
                    'inverse' => 'Не е после',
                ],

                'summary' => [
                    'direct' => ':attribute е после :date',
                    'inverse' => ':attribute не е после :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Е пред',
                    'inverse' => 'Не е пред',
                ],

                'summary' => [
                    'direct' => ':attribute е пред :date',
                    'inverse' => ':attribute не е пред :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Е датум',
                    'inverse' => 'Не е датум',
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

            'unit_labels' => [
                'second' => 'Секунди',
                'minute' => 'Минути',
                'hour' => 'Часа',
                'day' => 'Денови',
                'week' => 'Седмици',
                'month' => 'Месеци',
                'quarter' => 'Тромесечја',
                'year' => 'Години',
            ],

            'presets' => [
                'past_decade' => 'Измината декада',
                'past_5_years' => 'Изминати 5 години',
                'past_2_years' => 'Изминати 2 години',
                'past_year' => 'Измината година',
                'past_6_months' => 'Изминати 6 месеци',
                'past_quarter' => 'Изминато тромесечје',
                'past_month' => 'Изминат месец',
                'past_2_weeks' => 'Изминати 2 седмици',
                'past_week' => 'Измината седмица',
                'past_hour' => 'Изминат час',
                'past_minute' => 'Измината минута',
                'this_decade' => 'Оваа декада',
                'this_year' => 'Оваа година',
                'this_quarter' => 'Ова тромесечје',
                'this_month' => 'Овој месец',
                'today' => 'Денес',
                'this_hour' => 'Овој час',
                'this_minute' => 'Оваа минута',
                'next_minute' => 'Следна минута',
                'next_hour' => 'Следен час',
                'next_week' => 'Следна седмица',
                'next_2_weeks' => 'Следни 2 седмици',
                'next_month' => 'Следен месец',
                'next_quarter' => 'Следно тромесечје',
                'next_6_months' => 'Следни 6 месеци',
                'next_year' => 'Следна година',
                'next_2_years' => 'Следни 2 години',
                'next_5_years' => 'Следни 5 години',
                'next_decade' => 'Следна декада',
                'custom' => 'Прилагодено',
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

                'mode' => [

                    'label' => 'Тип на датум',

                    'options' => [
                        'absolute' => 'Специфичен датум',
                        'relative' => 'Поместувачки прозорец',
                    ],

                ],

                'preset' => [
                    'label' => 'Временски период',
                ],

                'relative_value' => [
                    'label' => 'Колку',
                ],

                'relative_unit' => [
                    'label' => 'Временска единица',
                ],

                'tense' => [

                    'label' => 'Време',

                    'options' => [
                        'past' => 'Минато',
                        'future' => 'Иднина',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Еднакво',
                    'inverse' => 'Не е еднакво',
                ],

                'summary' => [
                    'direct' => ':attribute е еднакво на :number',
                    'inverse' => ':attribute не е еднакво на :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Е максимум',
                    'inverse' => 'Е поголемо од',
                ],

                'summary' => [
                    'direct' => ':attribute е максимум :number',
                    'inverse' => ':attribute е поголемо од :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Е минимум',
                    'inverse' => 'Е помало од',
                ],

                'summary' => [
                    'direct' => ':attribute е минимум :number',
                    'inverse' => ':attribute е помало од :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Просек',
                    'summary' => 'Просек на :attribute',
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
                    'label' => 'Збир',
                    'summary' => 'Збир на :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Агрегат',
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
                    'direct' => 'Има максимум',
                    'inverse' => 'Има повеќе од',
                ],

                'summary' => [
                    'direct' => 'Има максимум :count :relationship',
                    'inverse' => 'Има повеќе од :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Има минимум',
                    'inverse' => 'Има помалку од',
                ],

                'summary' => [
                    'direct' => 'Има минимум :count :relationship',
                    'inverse' => 'Има помалку од :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Е празна',
                    'inverse' => 'Не е празна',
                ],

                'summary' => [
                    'direct' => ':relationship е празна',
                    'inverse' => ':relationship не е празна',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Е',
                        'inverse' => 'Не е',
                    ],

                    'multiple' => [
                        'direct' => 'Содржи',
                        'inverse' => 'Не содржи',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship е :values',
                        'inverse' => ':relationship не е :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship содржи :values',
                        'inverse' => ':relationship не содржи :values',
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
                    'direct' => 'Содржи',
                    'inverse' => 'Не содржи',
                ],

                'summary' => [
                    'direct' => ':attribute содржи :text',
                    'inverse' => ':attribute не содржи :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Завршува со',
                    'inverse' => 'Не завршува со',
                ],

                'summary' => [
                    'direct' => ':attribute завршува со :text',
                    'inverse' => ':attribute не завршува со :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Еднакво',
                    'inverse' => 'Не е еднакво',
                ],

                'summary' => [
                    'direct' => ':attribute е еднакво на :text',
                    'inverse' => ':attribute не е еднакво на :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Започнува со',
                    'inverse' => 'Не започнува со',
                ],

                'summary' => [
                    'direct' => ':attribute започнува со :text',
                    'inverse' => ':attribute не започнува со :text',
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
            'label' => 'Додади правило',
        ],

        'add_rule_group' => [
            'label' => 'Додади ИЛИ',
        ],

    ],

];
