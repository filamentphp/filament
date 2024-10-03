<?php

return [

    'label' => 'Query builder',

    'form' => [

        'operator' => [
            'label' => 'Operátor',
        ],

        'or_groups' => [

            'label' => 'Skupiny',

            'block' => [
                'label' => 'Disjunkce (NEBO)',
                'or' => 'NEBO',
            ],

        ],

        'rules' => [

            'label' => 'Pravidla',

            'item' => [
                'and' => 'A',
            ],

        ],

    ],

    'no_rules' => '(Žádná pravidla)',

    'item_separators' => [
        'and' => 'A',
        'or' => 'NEBO',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Je vyplněno',
                'inverse' => 'Je prázdné',
            ],

            'summary' => [
                'direct' => ':attribute je vyplněno',
                'inverse' => ':attribute je prázdné',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Je pravda',
                    'inverse' => 'Není pravda',
                ],

                'summary' => [
                    'direct' => ':attribute je pravda',
                    'inverse' => ':attribute není pravda',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Je po',
                    'inverse' => 'Není po',
                ],

                'summary' => [
                    'direct' => ':attribute je po :date',
                    'inverse' => ':attribute není po :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Je před',
                    'inverse' => 'Není před',
                ],

                'summary' => [
                    'direct' => ':attribute je před :date',
                    'inverse' => ':attribute není před :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Je datum',
                    'inverse' => 'Není datum',
                ],

                'summary' => [
                    'direct' => ':attribute je :date',
                    'inverse' => ':attribute není :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Je měsíc',
                    'inverse' => 'Není měsíc',
                ],

                'summary' => [
                    'direct' => ':attribute je :month',
                    'inverse' => ':attribute není :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Je rok',
                    'inverse' => 'Není rok',
                ],

                'summary' => [
                    'direct' => ':attribute je :year',
                    'inverse' => ':attribute není :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Datum',
                ],

                'month' => [
                    'label' => 'Měsíc',
                ],

                'year' => [
                    'label' => 'Rok',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Rovná se',
                    'inverse' => 'Nerovná se',
                ],

                'summary' => [
                    'direct' => ':attribute se rovná :number',
                    'inverse' => ':attribute se nerovná :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Je maximální',
                    'inverse' => 'Je větší než',
                ],

                'summary' => [
                    'direct' => ':attribute je maximálně :number',
                    'inverse' => ':attribute je větší než :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Je minimální',
                    'inverse' => 'Je menší než',
                ],

                'summary' => [
                    'direct' => ':attribute je minimálně :number',
                    'inverse' => ':attribute je menší než :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Průměr',
                    'summary' => 'Průměr :attribute',
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
                    'label' => 'Suma',
                    'summary' => 'Součet :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregace',
                ],

                'number' => [
                    'label' => 'Číslo',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Má',
                    'inverse' => 'Nemá',
                ],

                'summary' => [
                    'direct' => 'Má :count :relationship',
                    'inverse' => 'Nemá :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Má maximálně',
                    'inverse' => 'Má více než',
                ],

                'summary' => [
                    'direct' => 'Má maximálně :count :relationship',
                    'inverse' => 'Má více než :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Má minimálně',
                    'inverse' => 'Má méně než',
                ],

                'summary' => [
                    'direct' => 'Má minimálně :count :relationship',
                    'inverse' => 'Má méně než :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Je prázdné',
                    'inverse' => 'Není prázdné',
                ],

                'summary' => [
                    'direct' => ':relationship je prázdné',
                    'inverse' => ':relationship není prázdné',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Je',
                        'inverse' => 'Není',
                    ],

                    'multiple' => [
                        'direct' => 'Obsahuje',
                        'inverse' => 'Neobsahuje',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship je :values',
                        'inverse' => ':relationship není :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship obsahuje :values',
                        'inverse' => ':relationship neobsahuje :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' nebo ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Hodnota',
                    ],

                    'values' => [
                        'label' => 'Hodnoty',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Počet',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Je',
                    'inverse' => 'Není',
                ],

                'summary' => [
                    'direct' => ':attribute je :values',
                    'inverse' => ':attribute není :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' nebo ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Hodnota',
                    ],

                    'values' => [
                        'label' => 'Hodnoty',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Obsahuje',
                    'inverse' => 'Neobsahuje',
                ],

                'summary' => [
                    'direct' => ':attribute obsahuje :text',
                    'inverse' => ':attribute neobsahuje :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Končí na',
                    'inverse' => 'Nekončí na',
                ],

                'summary' => [
                    'direct' => ':attribute končí na :text',
                    'inverse' => ':attribute nekončí na :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Rovná se',
                    'inverse' => 'Nerovná se',
                ],

                'summary' => [
                    'direct' => ':attribute se rovná :text',
                    'inverse' => ':attribute se nerovná :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Začíná na',
                    'inverse' => 'Nezačíná na',
                ],

                'summary' => [
                    'direct' => ':attribute začíná na :text',
                    'inverse' => ':attribute nezačíná na :text',
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
            'label' => 'Přidat pravidlo',
        ],

        'add_rule_group' => [
            'label' => 'Přidat skupinu pravidel',
        ],

    ],

];
