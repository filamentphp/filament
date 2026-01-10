<?php

return [

    'label' => 'Constructor interogări',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Grupuri',

            'block' => [
                'label' => 'Condiție SAU',
                'or' => 'SAU',
            ],

        ],

        'rules' => [

            'label' => 'Reguli',

            'item' => [
                'and' => 'ȘI',
            ],

        ],

    ],

    'no_rules' => '(Fără reguli)',

    'item_separators' => [
        'and' => 'ȘI',
        'or' => 'SAU',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Este completat',
                'inverse' => 'Este gol',
            ],

            'summary' => [
                'direct' => ':attribute este completat',
                'inverse' => ':attribute este gol',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Este adevărat',
                    'inverse' => 'Este fals',
                ],

                'summary' => [
                    'direct' => ':attribute este adevărat',
                    'inverse' => ':attribute este fals',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Este după',
                    'inverse' => 'Nu este după',
                ],

                'summary' => [
                    'direct' => ':attribute este după :date',
                    'inverse' => ':attribute nu este după :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Este înainte',
                    'inverse' => 'Nu este înainte',
                ],

                'summary' => [
                    'direct' => ':attribute este înainte de :date',
                    'inverse' => ':attribute nu este înainte de :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Este data',
                    'inverse' => 'Nu este data',
                ],

                'summary' => [
                    'direct' => ':attribute este :date',
                    'inverse' => ':attribute nu este :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Este luna',
                    'inverse' => 'Nu este luna',
                ],

                'summary' => [
                    'direct' => ':attribute este :month',
                    'inverse' => ':attribute nu este :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Este anul',
                    'inverse' => 'Nu este anul',
                ],

                'summary' => [
                    'direct' => ':attribute este :year',
                    'inverse' => ':attribute nu este :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Secunde',
                'minute' => 'Minute',
                'hour' => 'Ore',
                'day' => 'Zile',
                'week' => 'Săptămâni',
                'month' => 'Luni',
                'quarter' => 'Trimestre',
                'year' => 'Ani',
            ],

            'presets' => [
                'past_decade' => 'Ultimul deceniu',
                'past_5_years' => 'Ultimii 5 ani',
                'past_2_years' => 'Ultimii 2 ani',
                'past_year' => 'Ultimul an',
                'past_6_months' => 'Ultimele 6 luni',
                'past_quarter' => 'Ultimul trimestru',
                'past_month' => 'Ultima lună',
                'past_2_weeks' => 'Ultimele 2 săptămâni',
                'past_week' => 'Ultima săptămână',
                'past_hour' => 'Ultima oră',
                'past_minute' => 'Ultimul minut',
                'this_decade' => 'Acest deceniu',
                'this_year' => 'Acest an',
                'this_quarter' => 'Acest trimestru',
                'this_month' => 'Această lună',
                'today' => 'Astăzi',
                'this_hour' => 'Această oră',
                'this_minute' => 'Acest minut',
                'next_minute' => 'Următorul minut',
                'next_hour' => 'Următoarea oră',
                'next_week' => 'Săptămâna viitoare',
                'next_2_weeks' => 'Următoarele 2 săptămâni',
                'next_month' => 'Luna viitoare',
                'next_quarter' => 'Trimestrul viitor',
                'next_6_months' => 'Următoarele 6 luni',
                'next_year' => 'Anul viitor',
                'next_2_years' => 'Următorii 2 ani',
                'next_5_years' => 'Următorii 5 ani',
                'next_decade' => 'Următorul deceniu',
                'custom' => 'Personalizat',
            ],

            'form' => [

                'date' => [
                    'label' => 'Dată',
                ],

                'month' => [
                    'label' => 'Lună',
                ],

                'year' => [
                    'label' => 'An',
                ],

                'mode' => [

                    'label' => 'Tip dată',

                    'options' => [
                        'absolute' => 'Dată specifică',
                        'relative' => 'Fereastră relativă',
                    ],

                ],

                'preset' => [
                    'label' => 'Perioadă de timp',
                ],

                'relative_value' => [
                    'label' => 'Câte',
                ],

                'relative_unit' => [
                    'label' => 'Unitate de timp',
                ],

                'tense' => [

                    'label' => 'Timp',

                    'options' => [
                        'past' => 'Trecut',
                        'future' => 'Viitor',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Este egal cu',
                    'inverse' => 'Nu este egal cu',
                ],

                'summary' => [
                    'direct' => ':attribute este egal cu :number',
                    'inverse' => ':attribute nu este egal cu :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Este maxim',
                    'inverse' => 'Este mai mare decât',
                ],

                'summary' => [
                    'direct' => ':attribute este maxim :number',
                    'inverse' => ':attribute este mai mare decât :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Este minim',
                    'inverse' => 'Este mai mic decât',
                ],

                'summary' => [
                    'direct' => ':attribute este minim :number',
                    'inverse' => ':attribute este mai mic decât :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Medie',
                    'summary' => 'Media :attribute',
                ],

                'max' => [
                    'label' => 'Max',
                    'summary' => 'Max :attribute',
                ],

                'min' => [
                    'label' => 'Min',
                    'summary' => 'Min :attribute',
                ],

                'sum' => [
                    'label' => 'Sumă',
                    'summary' => 'Suma :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregare',
                ],

                'number' => [
                    'label' => 'Număr',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Are',
                    'inverse' => 'Nu are',
                ],

                'summary' => [
                    'direct' => 'Are :count :relationship',
                    'inverse' => 'Nu are :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Are maxim',
                    'inverse' => 'Are mai mult de',
                ],

                'summary' => [
                    'direct' => 'Are maxim :count :relationship',
                    'inverse' => 'Are mai mult de :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Are minim',
                    'inverse' => 'Are mai puțin de',
                ],

                'summary' => [
                    'direct' => 'Are minim :count :relationship',
                    'inverse' => 'Are mai puțin de :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Este gol',
                    'inverse' => 'Nu este gol',
                ],

                'summary' => [
                    'direct' => ':relationship este gol',
                    'inverse' => ':relationship nu este gol',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Este',
                        'inverse' => 'Nu este',
                    ],

                    'multiple' => [
                        'direct' => 'Conține',
                        'inverse' => 'Nu conține',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship este :values',
                        'inverse' => ':relationship nu este :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship conține :values',
                        'inverse' => ':relationship nu conține :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' sau ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Valoare',
                    ],

                    'values' => [
                        'label' => 'Valori',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Număr',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Este',
                    'inverse' => 'Nu este',
                ],

                'summary' => [
                    'direct' => ':attribute este :values',
                    'inverse' => ':attribute nu este :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' sau ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Valoare',
                    ],

                    'values' => [
                        'label' => 'Valori',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Conține',
                    'inverse' => 'Nu conține',
                ],

                'summary' => [
                    'direct' => ':attribute conține :text',
                    'inverse' => ':attribute nu conține :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Se termină cu',
                    'inverse' => 'Nu se termină cu',
                ],

                'summary' => [
                    'direct' => ':attribute se termină cu :text',
                    'inverse' => ':attribute nu se termină cu :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Este egal cu',
                    'inverse' => 'Nu este egal cu',
                ],

                'summary' => [
                    'direct' => ':attribute este egal cu :text',
                    'inverse' => ':attribute nu este egal cu :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Începe cu',
                    'inverse' => 'Nu începe cu',
                ],

                'summary' => [
                    'direct' => ':attribute începe cu :text',
                    'inverse' => ':attribute nu începe cu :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Text',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Adaugă regulă',
        ],

        'add_rule_group' => [
            'label' => 'Adaugă SAU',
        ],

    ],

];
