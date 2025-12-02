<?php

return [

    'label' => 'Spørringsbygger',

    'form' => [

        'operator' => [
            'label' => 'Operatør',
        ],

        'or_groups' => [

            'label' => 'Grupper',

            'block' => [
                'label' => 'Disjunksjon (ELLER)',
                'or' => 'ELLER',
            ],

        ],

        'rules' => [

            'label' => 'Regler',

            'item' => [
                'and' => 'OG',
            ],

        ],

    ],

    'no_rules' => '(Ingen regler)',

    'item_separators' => [
        'and' => 'OG',
        'or' => 'ELLER',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Er fylt',
                'inverse' => 'Er blank',
            ],

            'summary' => [
                'direct' => ':attribute er fylt',
                'inverse' => ':attribute er blank',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Er sant',
                    'inverse' => 'Er usant',
                ],

                'summary' => [
                    'direct' => ':attribute er sant',
                    'inverse' => ':attribute er usant',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Er etter',
                    'inverse' => 'Er ikke etter',
                ],

                'summary' => [
                    'direct' => ':attribute er etter :date',
                    'inverse' => ':attribute er ikke etter :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Er før',
                    'inverse' => 'Er ikke før',
                ],

                'summary' => [
                    'direct' => ':attribute er før :date',
                    'inverse' => ':attribute er ikke før :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Er dato',
                    'inverse' => 'Er ikke dato',
                ],

                'summary' => [
                    'direct' => ':attribute er :date',
                    'inverse' => ':attribute er ikke :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Er måned',
                    'inverse' => 'Er ikke måned',
                ],

                'summary' => [
                    'direct' => ':attribute er :month',
                    'inverse' => ':attribute er ikke :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Er år',
                    'inverse' => 'er ikke år',
                ],

                'summary' => [
                    'direct' => ':attribute er :year',
                    'inverse' => ':attribute er ikke :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Dato',
                ],

                'month' => [
                    'label' => 'Måned',
                ],

                'year' => [
                    'label' => 'År',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Er lik',
                    'inverse' => 'Er ikke lik',
                ],

                'summary' => [
                    'direct' => ':attribute er lik :number',
                    'inverse' => ':attribute er ikke lik :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Er maks',
                    'inverse' => 'Er større enn',
                ],

                'summary' => [
                    'direct' => ':attribute er maks :number',
                    'inverse' => ':attribute er større enn :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Er minimum',
                    'inverse' => 'Er mindre enn',
                ],

                'summary' => [
                    'direct' => ':attribute er minimum :number',
                    'inverse' => ':attribute er mindre enn :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Gjennomsnitt',
                    'summary' => 'Gjennomsnitt :attribute',
                ],

                'max' => [
                    'label' => 'Maks',
                    'summary' => 'Maks :attribute',
                ],

                'min' => [
                    'label' => 'Minimum',
                    'summary' => 'Minimum :attribute',
                ],

                'sum' => [
                    'label' => 'Sum',
                    'summary' => 'Sum av :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Samlet',
                ],

                'number' => [
                    'label' => 'Nummer',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Har',
                    'inverse' => 'Har ikke',
                ],

                'summary' => [
                    'direct' => 'Har :count :relationship',
                    'inverse' => 'Har ikke :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Har maks',
                    'inverse' => 'Har mer enn',
                ],

                'summary' => [
                    'direct' => 'Har maks :count :relationship',
                    'inverse' => 'Har mer enn :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Har minimum',
                    'inverse' => 'Har mindre enn',
                ],

                'summary' => [
                    'direct' => 'Har minimum :count :relationship',
                    'inverse' => 'Har mindre enn :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Er tom',
                    'inverse' => 'Er ikke tom',
                ],

                'summary' => [
                    'direct' => ':relationship er tom',
                    'inverse' => ':relationship er ikke tom',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Er',
                        'inverse' => 'Er ikke',
                    ],

                    'multiple' => [
                        'direct' => 'Inneholder',
                        'inverse' => 'Inneholder ikke',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship er :values',
                        'inverse' => ':relationship er ikke :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship inneholder :values',
                        'inverse' => ':relationship inneholder ikke :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' eller ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Verdi',
                    ],

                    'values' => [
                        'label' => 'Verdier',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Telle',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Er',
                    'inverse' => 'Er ikke',
                ],

                'summary' => [
                    'direct' => ':attribute er :values',
                    'inverse' => ':attribute er ikke :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' eller ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Verdi',
                    ],

                    'values' => [
                        'label' => 'Verdier',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Inneholder',
                    'inverse' => 'Inneholder ikke',
                ],

                'summary' => [
                    'direct' => ':attribute inneholder :text',
                    'inverse' => ':attribute inneholder ikke :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Ender med',
                    'inverse' => 'Ender ikke med',
                ],

                'summary' => [
                    'direct' => ':attribute ender med :text',
                    'inverse' => ':attribute ender ikke med :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Er lik',
                    'inverse' => 'Er ikke lik',
                ],

                'summary' => [
                    'direct' => ':attribute er lik :text',
                    'inverse' => ':attribute er ikke lik :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Starter med',
                    'inverse' => 'Starter ikke med',
                ],

                'summary' => [
                    'direct' => ':attribute starter med :text',
                    'inverse' => ':attribute starter ikke med :text',
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
            'label' => 'Legg til regel',
        ],

        'add_rule_group' => [
            'label' => 'Legg til grupperegel',
        ],

    ],

];
