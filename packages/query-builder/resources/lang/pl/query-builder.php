<?php

return [

    'label' => 'Kreator zapytań',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Grupy',

            'block' => [
                'label' => 'Alternatywa (OR)',
                'or' => 'OR',
            ],

        ],

        'rules' => [

            'label' => 'Reguły',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(Brak reguł)',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Jest wypełnione',
                'inverse' => 'Jest puste',
            ],

            'summary' => [
                'direct' => ':attribute jest wypełnione',
                'inverse' => ':attribute jest puste',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Jest prawdą',
                    'inverse' => 'Jest fałszem',
                ],

                'summary' => [
                    'direct' => ':attribute jest prawdą',
                    'inverse' => ':attribute jest fałszem',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Jest po',
                    'inverse' => 'Nie jest po',
                ],

                'summary' => [
                    'direct' => ':attribute jest po :date',
                    'inverse' => ':attribute nie jest po :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Jest przed',
                    'inverse' => 'Nie jest przed',
                ],

                'summary' => [
                    'direct' => ':attribute jest przed :date',
                    'inverse' => ':attribute nie jest przed :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Jest datą',
                    'inverse' => 'Nie jest datą',
                ],

                'summary' => [
                    'direct' => ':attribute jest datą',
                    'inverse' => ':attribute nie jest datą',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Jest miesiącem',
                    'inverse' => 'Nie jest miesiącem',
                ],

                'summary' => [
                    'direct' => ':attribute jest :month',
                    'inverse' => ':attribute nie jest :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Jest rokiem',
                    'inverse' => 'Nie jest rokiem',
                ],

                'summary' => [
                    'direct' => ':attribute jest :year',
                    'inverse' => ':attribute nie jest :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Sekundy',
                'minute' => 'Minuty',
                'hour' => 'Godziny',
                'day' => 'Dni',
                'week' => 'Tygodnie',
                'month' => 'Miesiące',
                'quarter' => 'Kwartały',
                'year' => 'Lata',
            ],

            'presets' => [
                'past_decade' => 'Ostatnia dekada',
                'past_5_years' => 'Ostatnie 5 lat',
                'past_2_years' => 'Ostatnie 2 lata',
                'past_year' => 'Ostatni rok',
                'past_6_months' => 'Ostatnie 6 miesięcy',
                'past_quarter' => 'Ostatni kwartał',
                'past_month' => 'Ostatni miesiąc',
                'past_2_weeks' => 'Ostatnie 2 tygodnie',
                'past_week' => 'Ostatni tydzień',
                'past_hour' => 'Ostatnia godzina',
                'past_minute' => 'Ostatnia minuta',
                'this_decade' => 'Ta dekada',
                'this_year' => 'Ten rok',
                'this_quarter' => 'Ten kwartał',
                'this_month' => 'Ten miesiąc',
                'today' => 'Dzisiaj',
                'this_hour' => 'Obecna godzina',
                'this_minute' => 'Obecna minuta',
                'next_minute' => 'Następna minuta',
                'next_hour' => 'Następna godzina',
                'next_week' => 'Następny tydzień',
                'next_2_weeks' => 'Następne 2 tygodnie',
                'next_month' => 'Następny miesiąc',
                'next_quarter' => 'Następny kwartał',
                'next_6_months' => 'Następne 6 miesięcy',
                'next_year' => 'Następny rok',
                'next_2_years' => 'Następne 2 lata',
                'next_5_years' => 'Następne 5 lat',
                'next_decade' => 'Następna dekada',
                'custom' => 'Niestandardowy zakres',
            ],

            'form' => [

                'date' => [
                    'label' => 'Data',
                ],

                'month' => [
                    'label' => 'Miesiąc',
                ],

                'year' => [
                    'label' => 'Rok',
                ],

                'mode' => [

                    'label' => 'Typ daty',

                    'options' => [
                        'absolute' => 'Konkretna data',
                        'relative' => 'Okno przesuwne',
                    ],

                ],

                'preset' => [
                    'label' => 'Okres czasu',
                ],

                'relative_value' => [
                    'label' => 'Ile',
                ],

                'relative_unit' => [
                    'label' => 'Jednostka czasu',
                ],

                'tense' => [

                    'label' => 'Czas',

                    'options' => [
                        'past' => 'Przeszłość',
                        'future' => 'Przyszłość',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Jest równe',
                    'inverse' => 'Nie jest równe',
                ],

                'summary' => [
                    'direct' => ':attribute jest równe :number',
                    'inverse' => ':attribute nie jest równe :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Jest maksymalnie',
                    'inverse' => 'Jest większe niż',
                ],

                'summary' => [
                    'direct' => ':attribute jest maksymalnie :number',
                    'inverse' => ':attribute jest większe niż :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Jest minimalnie',
                    'inverse' => 'Jest mniejsze niż',
                ],

                'summary' => [
                    'direct' => ':attribute jest minimalnie :number',
                    'inverse' => ':attribute jest mniejsze niż :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Średnia',
                    'summary' => 'Średnia :attribute',
                ],

                'max' => [
                    'label' => 'Maksimum',
                    'summary' => 'Maksimum :attribute',
                ],

                'min' => [
                    'label' => 'Minimum',
                    'summary' => 'Minimum :attribute',
                ],

                'sum' => [
                    'label' => 'Suma',
                    'summary' => 'Suma :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Średnia',
                ],

                'number' => [
                    'label' => 'Numer',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Ma',
                    'inverse' => 'Nie ma',
                ],

                'summary' => [
                    'direct' => 'Ma :count :relationship',
                    'inverse' => 'Nie ma :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Ma maksymalnie',
                    'inverse' => 'Ma więcej niż',
                ],

                'summary' => [
                    'direct' => 'Ma maksymalnie :count :relationship',
                    'inverse' => 'Ma więcej niż :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Ma minimum',
                    'inverse' => 'Ma mniej niż',
                ],

                'summary' => [
                    'direct' => 'Ma minimum :count :relationship',
                    'inverse' => 'Ma mniej niż :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Jest puste',
                    'inverse' => 'Nie jest puste',
                ],

                'summary' => [
                    'direct' => ':relationship jest puste',
                    'inverse' => ':relationship nie jest puste',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Jest',
                        'inverse' => 'Nie jest',
                    ],

                    'multiple' => [
                        'direct' => 'Zawiera',
                        'inverse' => 'Nie zawiera',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship jest :values',
                        'inverse' => ':relationship nie jest :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship zawiera :values',
                        'inverse' => ':relationship nie zawiera :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' lub ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Wartość',
                    ],

                    'values' => [
                        'label' => 'Wartości',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Liczba',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Jest',
                    'inverse' => 'Nie jest',
                ],

                'summary' => [
                    'direct' => ':attribute jest :values',
                    'inverse' => ':attribute nie jest :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' lub ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Wartość',
                    ],

                    'values' => [
                        'label' => 'Wartości',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Zawiera',
                    'inverse' => 'Nie zawiera',
                ],

                'summary' => [
                    'direct' => ':attribute zawiera :text',
                    'inverse' => ':attribute nie zawiera :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Kończy się na',
                    'inverse' => 'Nie kończy się na',
                ],

                'summary' => [
                    'direct' => ':attribute kończy się na :text',
                    'inverse' => ':attribute nie kończy się na :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Jest równe',
                    'inverse' => 'Nie jest równe',
                ],

                'summary' => [
                    'direct' => ':attribute jest równe :text',
                    'inverse' => ':attribute nie jest równe :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Zaczyna się od',
                    'inverse' => 'Nie zaczyna się od',
                ],

                'summary' => [
                    'direct' => ':attribute zaczyna się od :text',
                    'inverse' => ':attribute nie zaczyna się od :text',
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
            'label' => 'Dodaj regułę',
        ],

        'add_rule_group' => [
            'label' => 'Dodaj grupę reguł',
        ],

    ],

];
