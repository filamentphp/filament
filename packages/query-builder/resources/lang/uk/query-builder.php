<?php

return [

    'label' => 'Конструктор запитів',

    'form' => [

        'operator' => [
            'label' => 'Оператор',
        ],

        'or_groups' => [

            'label' => 'Групи',

            'block' => [
                'label' => 'Дизґ\'юнкція (OR)',
                'or' => 'АБО',
            ],

        ],

        'rules' => [

            'label' => 'Правила',

            'item' => [
                'and' => 'ТА',
            ],

        ],

    ],

    'no_rules' => '(Правила відсутні)',

    'item_separators' => [
        'and' => 'ТА',
        'or' => 'АБО',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Заповнений',
                'inverse' => 'Пустий',
            ],

            'summary' => [
                'direct' => ':attribute заповнений',
                'inverse' => ':attribute пустий',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Правда',
                    'inverse' => 'Неправда',
                ],

                'summary' => [
                    'direct' => ':attribute правда',
                    'inverse' => ':attribute неправда',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Після того',
                    'inverse' => 'Не після того',
                ],

                'summary' => [
                    'direct' => ':attribute після :date',
                    'inverse' => ':attribute не після :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'До того',
                    'inverse' => 'Не раніше',
                ],

                'summary' => [
                    'direct' => ':attribute до :date',
                    'inverse' => ':attribute не раніше :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Є датою',
                    'inverse' => 'Не є датою',
                ],

                'summary' => [
                    'direct' => ':attribute є :date',
                    'inverse' => ':attribute не є :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Є місяцем',
                    'inverse' => 'Не є місяцем',
                ],

                'summary' => [
                    'direct' => ':attribute є :month',
                    'inverse' => ':attribute не є :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Є роком',
                    'inverse' => 'Не є роком',
                ],

                'summary' => [
                    'direct' => ':attribute є :year',
                    'inverse' => ':attribute не є :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Число',
                ],

                'month' => [
                    'label' => 'Місяць',
                ],

                'year' => [
                    'label' => 'Рік',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Дорівнює',
                    'inverse' => 'Не дорівнює',
                ],

                'summary' => [
                    'direct' => ':attribute дорівнює :number',
                    'inverse' => ':attribute не дорівнює :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Максимальний',
                    'inverse' => 'Більше, ніж',
                ],

                'summary' => [
                    'direct' => ':attribute максимальний :number',
                    'inverse' => ':attribute більше ніж :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Мінімальний',
                    'inverse' => 'Менший, ніж',
                ],

                'summary' => [
                    'direct' => ':attribute мінімальний :number',
                    'inverse' => ':attribute менший ніж :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Середнє',
                    'summary' => 'Середнє :attribute',
                ],

                'max' => [
                    'label' => 'Максимальний',
                    'summary' => 'Максимальний :attribute',
                ],

                'min' => [
                    'label' => 'Мінімальний',
                    'summary' => 'Мінімальний :attribute',
                ],

                'sum' => [
                    'label' => 'Сума',
                    'summary' => 'Сума :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Агрегація',
                ],

                'number' => [
                    'label' => 'Число',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Має',
                    'inverse' => 'Не має',
                ],

                'summary' => [
                    'direct' => 'Має :count :relationship',
                    'inverse' => 'Не має :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Має максимум',
                    'inverse' => 'Має більше, ніж',
                ],

                'summary' => [
                    'direct' => 'Має максимум :count :relationship',
                    'inverse' => 'Має більше ніж :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Має мінімум',
                    'inverse' => 'Має менше, ніж',
                ],

                'summary' => [
                    'direct' => 'Має мінімум :count :relationship',
                    'inverse' => 'Має менше ніж :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Порожній',
                    'inverse' => 'Непорожній',
                ],

                'summary' => [
                    'direct' => ':relationship порожній',
                    'inverse' => ':relationship непорожній',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Є',
                        'inverse' => 'Не є',
                    ],

                    'multiple' => [
                        'direct' => 'Містить',
                        'inverse' => 'Не містить',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship є :values',
                        'inverse' => ':relationship не є :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship містить :values',
                        'inverse' => ':relationship не містить :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' або ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Значення',
                    ],

                    'values' => [
                        'label' => 'Декілька значеннь',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Кількість',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Є',
                    'inverse' => 'Не є',
                ],

                'summary' => [
                    'direct' => ':attribute є :values',
                    'inverse' => ':attribute не є :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' або ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Значення',
                    ],

                    'values' => [
                        'label' => 'Декілька значеннь',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Містить',
                    'inverse' => 'Не містить',
                ],

                'summary' => [
                    'direct' => ':attribute містить :text',
                    'inverse' => ':attribute не містить :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Закінчується на',
                    'inverse' => 'Не закінчується на',
                ],

                'summary' => [
                    'direct' => ':attribute закінчується на :text',
                    'inverse' => ':attribute не закінчується на :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Дорівнює',
                    'inverse' => 'Не дорівнює',
                ],

                'summary' => [
                    'direct' => ':attribute дорівнює :text',
                    'inverse' => ':attribute не дорівнює :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Починається з',
                    'inverse' => 'Не починається з',
                ],

                'summary' => [
                    'direct' => ':attribute починається з :text',
                    'inverse' => ':attribute не починається з :text',
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
            'label' => 'Додати правили',
        ],

        'add_rule_group' => [
            'label' => 'Додати групу правил',
        ],

    ],

];
