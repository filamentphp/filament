<?php

return [

    'label' => 'Összetett szűrés',

    'form' => [

        'operator' => [
            'label' => 'Művelet',
        ],

        'or_groups' => [

            'label' => 'Csoportok',

            'block' => [
                'label' => 'Diszjunkció (VAGY)',
                'or' => 'VAGY',
            ],

        ],

        'rules' => [

            'label' => 'Szabályok',

            'item' => [
                'and' => 'ÉS',
            ],

        ],

    ],

    'no_rules' => '(Nincs megadva szabály)',

    'item_separators' => [
        'and' => 'ÉS',
        'or' => 'VAGY',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Nem üres',
                'inverse' => 'Üres',
            ],

            'summary' => [
                'direct' => ':attribute nem üres',
                'inverse' => ':attribute üres',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Igaz',
                    'inverse' => 'Hamis',
                ],

                'summary' => [
                    'direct' => ':attribute igaz',
                    'inverse' => ':attribute hamis',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Ez után',
                    'inverse' => 'Nem ez után',
                ],

                'summary' => [
                    'direct' => ':attribute :date után van',
                    'inverse' => ':attribute nem :date után van',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Ez előtt',
                    'inverse' => 'Nem ez előtt',
                ],

                'summary' => [
                    'direct' => ':attribute :date előtt van',
                    'inverse' => ':attribute nem :date előtt van',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Ekkor',
                    'inverse' => 'Nem ekkor',
                ],

                'summary' => [
                    'direct' => ':attribute :date',
                    'inverse' => ':attribute nem :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Ezen hónapban',
                    'inverse' => 'Nem ezen hónapban',
                ],

                'summary' => [
                    'direct' => ':attribute :month hónapban van',
                    'inverse' => ':attribute nem :month hónapban van',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Ezen évben',
                    'inverse' => 'Nem ezen évben',
                ],

                'summary' => [
                    'direct' => ':attribute :year évben van',
                    'inverse' => ':attribute nem :year évben van',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Dátum',
                ],

                'month' => [
                    'label' => 'Hónap',
                ],

                'year' => [
                    'label' => 'Év',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Egyenlő',
                    'inverse' => 'Nem egyenlő',
                ],

                'summary' => [
                    'direct' => ':attribute egyenlő :number',
                    'inverse' => ':attributenem egyenlő :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Maximum',
                    'inverse' => 'Nagyobb mint',
                ],

                'summary' => [
                    'direct' => ':attribute maximum :number',
                    'inverse' => ':attribute nagyobb mint :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Minimum',
                    'inverse' => 'Kisebb mint',
                ],

                'summary' => [
                    'direct' => ':attribute minimum :number',
                    'inverse' => ':attribute kisebb mint :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Átlag',
                    'summary' => 'Átlag :attribute',
                ],

                'max' => [
                    'label' => 'Maximum',
                    'summary' => 'Maximum :attribute',
                ],

                'min' => [
                    'label' => 'Minimum',
                    'summary' => 'Minimum :attribute',
                ],

                'sum' => [
                    'label' => 'Összeg',
                    'summary' => ':attribute összege',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Összesítés',
                ],

                'number' => [
                    'label' => 'Szám',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Rendelkezik',
                    'inverse' => 'Nem rendelkezik',
                ],

                'summary' => [
                    'direct' => 'Rendelkezik :count :relationship -val/vel',
                    'inverse' => 'Nem rendelkezik :count :relationship -val/vel',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Maximum rendelkezik',
                    'inverse' => 'Többel rendelkezik',
                ],

                'summary' => [
                    'direct' => 'Maximum :count :relationship -val/vel rendelkezik',
                    'inverse' => 'Több mint :count :relationship -val/vel rendelkezik',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Minimum rendelkezik',
                    'inverse' => 'Kevesebbel rendelkezik',
                ],

                'summary' => [
                    'direct' => 'Minimum :count :relationship -val/vel rendelkezik',
                    'inverse' => 'Kevesebb mint :count :relationship -val/vel rendelkezik',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Meg van adva',
                    'inverse' => 'Nincs megadva',
                ],

                'summary' => [
                    'direct' => ':relationship meg van adva',
                    'inverse' => ':relationship nincs megadva',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Ez',
                        'inverse' => 'Nem ez',
                    ],

                    'multiple' => [
                        'direct' => 'Tartalmazza',
                        'inverse' => 'Nem tartalmazza',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship értéke :values',
                        'inverse' => ':relationship értéke nem :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship tartalmazza a(z) :values értékeket',
                        'inverse' => ':relationship nem tartalmazza a(z) :values értékeket',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' vagy ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Érték',
                    ],

                    'values' => [
                        'label' => 'Értékek',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Mennyiség',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Ez',
                    'inverse' => 'Nem ez',
                ],

                'summary' => [
                    'direct' => ':attribute értéke :values',
                    'inverse' => ':attribute értéke nem :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' vagy ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Érték',
                    ],

                    'values' => [
                        'label' => 'Értékek',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Tartalmazza',
                    'inverse' => 'Nem tartalmazza',
                ],

                'summary' => [
                    'direct' => ':attribute tartalmazza a(z) :text szövegrészletet',
                    'inverse' => ':attribute nem tartalmazza a(z) :text szövegrészletet',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Így végződik',
                    'inverse' => 'Nem így végződik',
                ],

                'summary' => [
                    'direct' => ':attribute :text -val/vel végződik',
                    'inverse' => ':attribute nem :text -val/vel végződik',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Megegyezik',
                    'inverse' => 'Nem egyezik meg',
                ],

                'summary' => [
                    'direct' => ':attribute megegyezik :text -val/vel',
                    'inverse' => ':attribute nem egyezik meg :text -val/vel',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Így kezdődik',
                    'inverse' => 'Nem így kezdődik',
                ],

                'summary' => [
                    'direct' => ':attribute :text -val/vel kezdődik',
                    'inverse' => ':attribute nem :text -val/vel kezdődik',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Szöveg',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Szabály hozzáadása',
        ],

        'add_rule_group' => [
            'label' => 'Szabálycsoport hozzáadása',
        ],

    ],

];
