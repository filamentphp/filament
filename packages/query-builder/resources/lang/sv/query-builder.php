<?php

return [

    'label' => 'Frågebyggare',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Grupper',

            'block' => [
                'label' => 'Disjunktion (ELLER)',
                'or' => 'ELLER',
            ],

        ],

        'rules' => [

            'label' => 'Regler',

            'item' => [
                'and' => 'OCH',
            ],

        ],

    ],

    'no_rules' => '(Inga regler)',

    'item_separators' => [
        'and' => 'OCH',
        'or' => 'ELLER',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Är ifyllt',
                'inverse' => 'Är tomt',
            ],

            'summary' => [
                'direct' => ':attribute är ifyllt',
                'inverse' => ':attribute är tomt',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Är sann',
                    'inverse' => 'Är falsk',
                ],

                'summary' => [
                    'direct' => ':attribute är sann',
                    'inverse' => ':attribute är falsk',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Är efter',
                    'inverse' => 'Är inte efter',
                ],

                'summary' => [
                    'direct' => ':attribute är efter :date',
                    'inverse' => ':attribute är inte efter :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Är före',
                    'inverse' => 'Är inte före',
                ],

                'summary' => [
                    'direct' => ':attribute är före :date',
                    'inverse' => ':attribute är inte före :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Är datum',
                    'inverse' => 'Är inte datum',
                ],

                'summary' => [
                    'direct' => ':attribute är :date',
                    'inverse' => ':attribute är inte :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Är månad',
                    'inverse' => 'Är inte månad',
                ],

                'summary' => [
                    'direct' => ':attribute är :month',
                    'inverse' => ':attribute är inte :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Är år',
                    'inverse' => 'Är inte år',
                ],

                'summary' => [
                    'direct' => ':attribute är :year',
                    'inverse' => ':attribute är inte :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Datum',
                ],

                'month' => [
                    'label' => 'Månad',
                ],

                'year' => [
                    'label' => 'År',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Är lika med',
                    'inverse' => 'Är inte lika med',
                ],

                'summary' => [
                    'direct' => ':attribute är lika med :number',
                    'inverse' => ':attribute är inte lika med :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Är maximalt',
                    'inverse' => 'Är större än',
                ],

                'summary' => [
                    'direct' => ':attribute är maximalt :number',
                    'inverse' => ':attribute är större än :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Är minst',
                    'inverse' => 'Är mindre än',
                ],

                'summary' => [
                    'direct' => ':attribute är minst :number',
                    'inverse' => ':attribute är mindre än :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Genomsnitt',
                    'summary' => 'Genomsnitt av :attribute',
                ],

                'max' => [
                    'label' => 'Max',
                    'summary' => 'Max av :attribute',
                ],

                'min' => [
                    'label' => 'Min',
                    'summary' => 'Min av :attribute',
                ],

                'sum' => [
                    'label' => 'Summa',
                    'summary' => 'Summa av :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Aggregat',
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
                    'inverse' => 'Har inte',
                ],

                'summary' => [
                    'direct' => 'Har :count :relationship',
                    'inverse' => 'Har inte :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Har maximalt',
                    'inverse' => 'Har fler än',
                ],

                'summary' => [
                    'direct' => 'Har maximalt :count :relationship',
                    'inverse' => 'Har fler än :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Har minst',
                    'inverse' => 'Har färre än',
                ],

                'summary' => [
                    'direct' => 'Har minst :count :relationship',
                    'inverse' => 'Har färre än :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Är tom',
                    'inverse' => 'Är inte tom',
                ],

                'summary' => [
                    'direct' => ':relationship är tom',
                    'inverse' => ':relationship är inte tom',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Är',
                        'inverse' => 'Är inte',
                    ],

                    'multiple' => [
                        'direct' => 'Innehåller',
                        'inverse' => 'Innehåller inte',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship är :values',
                        'inverse' => ':relationship är inte :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship innehåller :values',
                        'inverse' => ':relationship innehåller inte :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' eller ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Värde',
                    ],

                    'values' => [
                        'label' => 'Värden',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Antal',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Är',
                    'inverse' => 'Är inte',
                ],

                'summary' => [
                    'direct' => ':attribute är :values',
                    'inverse' => ':attribute är inte :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' eller ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Värde',
                    ],

                    'values' => [
                        'label' => 'Värden',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Innehåller',
                    'inverse' => 'Innehåller inte',
                ],

                'summary' => [
                    'direct' => ':attribute innehåller :text',
                    'inverse' => ':attribute innehåller inte :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Slutar med',
                    'inverse' => 'Slutar inte med',
                ],

                'summary' => [
                    'direct' => ':attribute slutar med :text',
                    'inverse' => ':attribute slutar inte med :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Är lika med',
                    'inverse' => 'Är inte lika med',
                ],

                'summary' => [
                    'direct' => ':attribute är lika med :text',
                    'inverse' => ':attribute är inte lika med :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Börjar med',
                    'inverse' => 'Börjar inte med',
                ],

                'summary' => [
                    'direct' => ':attribute börjar med :text',
                    'inverse' => ':attribute börjar inte med :text',
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
            'label' => 'Lägg till regel',
        ],

        'add_rule_group' => [
            'label' => 'Lägg till regelgrupp',
        ],

    ],

];
