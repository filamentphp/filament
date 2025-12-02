<?php

return [

    'label' => 'Constructor de consultes',

    'form' => [

        'operator' => [
            'label' => 'Operador',
        ],

        'or_groups' => [

            'label' => 'Grups',

            'block' => [
                'label' => 'Disjunció (O)',
                'or' => 'O',
            ],

        ],

        'rules' => [

            'label' => 'Regles',

            'item' => [
                'and' => 'Y',
            ],

        ],

    ],

    'no_rules' => '(Sense regles)',

    'item_separators' => [
        'and' => 'Y',
        'or' => 'O',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Està emplenat',
                'inverse' => 'Està en blanc',
            ],

            'summary' => [
                'direct' => ':attribute està emplenat',
                'inverse' => ':attribute està en blanc',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'És cert',
                    'inverse' => 'És fals',
                ],

                'summary' => [
                    'direct' => ':attribute és cert',
                    'inverse' => ':attribute és fals',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'És posterior a',
                    'inverse' => 'No és posterior a',
                ],

                'summary' => [
                    'direct' => ':attribute és posterior a :date',
                    'inverse' => ':attribute no és posterior a :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'És anterior a',
                    'inverse' => 'No és anterior a',
                ],

                'summary' => [
                    'direct' => ':attribute és anterior a :date',
                    'inverse' => ':attribute no és anterior a :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'És a la data',
                    'inverse' => 'No és a la data',
                ],

                'summary' => [
                    'direct' => ':attribute és a :date',
                    'inverse' => ':attribute no és a :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'És en el mes de',
                    'inverse' => 'No és en el mes de',
                ],

                'summary' => [
                    'direct' => ':attribute és al :month',
                    'inverse' => ':attribute no és al :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => "És a l'any",
                    'inverse' => "No és a l'any",
                ],

                'summary' => [
                    'direct' => ':attribute és al :year',
                    'inverse' => ':attribute no és al :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Data',
                ],

                'month' => [
                    'label' => 'Mes',
                ],

                'year' => [
                    'label' => 'Any',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Igual a',
                    'inverse' => 'No és igual a',
                ],

                'summary' => [
                    'direct' => ':attribute és igual a :number',
                    'inverse' => ':attribute no és igual a :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'És màxim',
                    'inverse' => 'És major que',
                ],

                'summary' => [
                    'direct' => ':attribute és màxim :number',
                    'inverse' => ':attribute és major que :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'És mínim',
                    'inverse' => 'És menor que',
                ],

                'summary' => [
                    'direct' => ':attribute és mínim :number',
                    'inverse' => ':attribute és menor que :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Promig',
                    'summary' => ':Attribute promig',
                ],

                'max' => [
                    'label' => 'Màx',
                    'summary' => 'Màxim :attribute',
                ],

                'min' => [
                    'label' => 'Mín',
                    'summary' => 'Mínim :attribute',
                ],

                'sum' => [
                    'label' => 'Suma',
                    'summary' => 'Suma de :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregat',
                ],

                'number' => [
                    'label' => 'Número',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Té',
                    'inverse' => 'No té',
                ],

                'summary' => [
                    'direct' => 'Té :count :relationship',
                    'inverse' => 'No té :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Té màxim',
                    'inverse' => 'Té més de',
                ],

                'summary' => [
                    'direct' => 'Té màxim :count :relationship',
                    'inverse' => 'Té més de :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Té mínim',
                    'inverse' => 'Té menys de',
                ],

                'summary' => [
                    'direct' => 'Té mínim :count :relationship',
                    'inverse' => 'Té menys de :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'És buit',
                    'inverse' => 'No és buit',
                ],

                'summary' => [
                    'direct' => ':relationship és buit',
                    'inverse' => ':relationship no és buit',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'És',
                        'inverse' => 'No és',
                    ],

                    'multiple' => [
                        'direct' => 'Conté',
                        'inverse' => 'No conté',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship són :values',
                        'inverse' => ':relationship no són :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship conté :values',
                        'inverse' => ':relationship no conté :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' o ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Valor',
                    ],

                    'values' => [
                        'label' => 'Valors',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Recompte',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'És',
                    'inverse' => 'No és',
                ],

                'summary' => [
                    'direct' => ':attribute és :values',
                    'inverse' => ':attribute no és :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' o ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Valor',
                    ],

                    'values' => [
                        'label' => 'Valors',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Conté',
                    'inverse' => 'No conté',
                ],

                'summary' => [
                    'direct' => ':attribute conté :text',
                    'inverse' => ':attribute no conté :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Acaba en',
                    'inverse' => 'No acaba en',
                ],

                'summary' => [
                    'direct' => ':attribute acaba en :text',
                    'inverse' => ':attribute no acaba en :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'És igual a',
                    'inverse' => 'No és igual a',
                ],

                'summary' => [
                    'direct' => ':attribute és igual a :text',
                    'inverse' => ':attribute no és igual a :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Comença amb',
                    'inverse' => 'No comença amb',
                ],

                'summary' => [
                    'direct' => ':attribute comença amb :text',
                    'inverse' => ':attribute no comença amb :text',
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
            'label' => 'Agregar regla',
        ],

        'add_rule_group' => [
            'label' => 'Agregar grup de regles',
        ],

    ],

];
