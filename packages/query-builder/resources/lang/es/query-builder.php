<?php

return [

    'label' => 'Constructor de consultas',

    'form' => [

        'operator' => [
            'label' => 'Operador',
        ],

        'or_groups' => [

            'label' => 'Grupos',

            'block' => [
                'label' => 'Disyunción (O)',
                'or' => 'O',
            ],

        ],

        'rules' => [

            'label' => 'Reglas',

            'item' => [
                'and' => 'Y',
            ],

        ],

    ],

    'no_rules' => '(Sin reglas)',

    'item_separators' => [
        'and' => 'Y',
        'or' => 'O',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Está rellenado',
                'inverse' => 'Está en blanco',
            ],

            'summary' => [
                'direct' => ':attribute está rellenado',
                'inverse' => ':attribute está en blanco',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Es verdadero',
                    'inverse' => 'Es falso',
                ],

                'summary' => [
                    'direct' => ':attribute es verdadero',
                    'inverse' => ':attribute es falso',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Es posterior a',
                    'inverse' => 'No es posterior a',
                ],

                'summary' => [
                    'direct' => ':attribute es posterior a :date',
                    'inverse' => ':attribute no es posterior a :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Es anterior a',
                    'inverse' => 'No es anterior a',
                ],

                'summary' => [
                    'direct' => ':attribute es anterior a :date',
                    'inverse' => ':attribute no es anterior a :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Es en la fecha',
                    'inverse' => 'No es en la fecha',
                ],

                'summary' => [
                    'direct' => ':attribute es en :date',
                    'inverse' => ':attribute no es en :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Es en el mes de',
                    'inverse' => 'No es en el mes de',
                ],

                'summary' => [
                    'direct' => ':attribute es en :month',
                    'inverse' => ':attribute no es en :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Es en el año',
                    'inverse' => 'No es en el año',
                ],

                'summary' => [
                    'direct' => ':attribute es en :year',
                    'inverse' => ':attribute no es en :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Fecha',
                ],

                'month' => [
                    'label' => 'Mes',
                ],

                'year' => [
                    'label' => 'Año',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Igual a',
                    'inverse' => 'No es igual a',
                ],

                'summary' => [
                    'direct' => ':attribute es igual a :number',
                    'inverse' => ':attribute no es igual a :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Es máximo',
                    'inverse' => 'Es mayor que',
                ],

                'summary' => [
                    'direct' => ':attribute es máximo :number',
                    'inverse' => ':attribute es mayor que :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Es mínimo',
                    'inverse' => 'Es menor que',
                ],

                'summary' => [
                    'direct' => ':attribute es mínimo :number',
                    'inverse' => ':attribute es menor que :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Promedio',
                    'summary' => ':Attribute promedio',
                ],

                'max' => [
                    'label' => 'Máx',
                    'summary' => 'Máximo :attribute',
                ],

                'min' => [
                    'label' => 'Mín',
                    'summary' => 'Mínimo :attribute',
                ],

                'sum' => [
                    'label' => 'Suma',
                    'summary' => 'Suma de :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregado',
                ],

                'number' => [
                    'label' => 'Número',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Tiene',
                    'inverse' => 'No tiene',
                ],

                'summary' => [
                    'direct' => 'Tiene :count :relationship',
                    'inverse' => 'No tiene :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Tiene máximo',
                    'inverse' => 'Tiene más de',
                ],

                'summary' => [
                    'direct' => 'Tiene máximo :count :relationship',
                    'inverse' => 'Tiene más de :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Tiene mínimo',
                    'inverse' => 'Tiene menos de',
                ],

                'summary' => [
                    'direct' => 'Tiene mínimo :count :relationship',
                    'inverse' => 'Tiene menos de :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Está vacío',
                    'inverse' => 'No está vacío',
                ],

                'summary' => [
                    'direct' => ':relationship está vacío',
                    'inverse' => ':relationship no está vacío',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Es',
                        'inverse' => 'No es',
                    ],

                    'multiple' => [
                        'direct' => 'Contiene',
                        'inverse' => 'No contiene',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship son :values',
                        'inverse' => ':relationship no son :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship contiene :values',
                        'inverse' => ':relationship no contiene :values',
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
                        'label' => 'Valores',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Conteo',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Es',
                    'inverse' => 'No es',
                ],

                'summary' => [
                    'direct' => ':attribute es :values',
                    'inverse' => ':attribute no es :values',
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
                        'label' => 'Valores',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Contiene',
                    'inverse' => 'No contiene',
                ],

                'summary' => [
                    'direct' => ':attribute contiene :text',
                    'inverse' => ':attribute no contiene :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Termina en',
                    'inverse' => 'No termina en',
                ],

                'summary' => [
                    'direct' => ':attribute termina en :text',
                    'inverse' => ':attribute no termina en :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Es igual a',
                    'inverse' => 'No es igual a',
                ],

                'summary' => [
                    'direct' => ':attribute es igual a :text',
                    'inverse' => ':attribute no es igual a :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Comienza con',
                    'inverse' => 'No comienza con',
                ],

                'summary' => [
                    'direct' => ':attribute comienza con :text',
                    'inverse' => ':attribute no comienza con :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Texto',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Agregar regla',
        ],

        'add_rule_group' => [
            'label' => 'Agregar grupo de reglas',
        ],

    ],

];
