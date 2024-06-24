<?php

return [

    'label' => 'Query builder',

    'form' => [

        'operator' => [
            'label' => 'Opérateur',
        ],

        'or_groups' => [

            'label' => 'Groupes',

            'block' => [
                'label' => 'Groupe (OR)',
                'or' => 'OU',
            ],

        ],

        'rules' => [

            'label' => 'Règles',

            'item' => [
                'and' => 'ET',
            ],

        ],

    ],

    'no_rules' => '(Aucune règle)',

    'item_separators' => [
        'and' => 'ET',
        'or' => 'OU',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Est rempli',
                'inverse' => 'Est vide',
            ],

            'summary' => [
                'direct' => ':attribute est rempli',
                'inverse' => ':attribute est vide',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Est true',
                    'inverse' => 'Est false',
                ],

                'summary' => [
                    'direct' => ':attribute est true',
                    'inverse' => ':attribute est false',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Est après le',
                    'inverse' => 'N\'est pas après le',
                ],

                'summary' => [
                    'direct' => ':attribute est après le :date',
                    'inverse' => ':attribute n\'est pas après le :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Est avant le',
                    'inverse' => 'N\'est pas avant le',
                ],

                'summary' => [
                    'direct' => ':attribute est avant le :date',
                    'inverse' => ':attribute n\'est pas avant le :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Est le (date)',
                    'inverse' => 'N\'est pas le (date)',
                ],

                'summary' => [
                    'direct' => ':attribute est le :date',
                    'inverse' => ':attribute n\'est pas le :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Est durant le mois de',
                    'inverse' => 'N\'est pas durant le mois de',
                ],

                'summary' => [
                    'direct' => ':attribute est en :month',
                    'inverse' => ':attribute n\'est pas en :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Est durant l\'année',
                    'inverse' => 'N\'est pas durant l\'année',
                ],

                'summary' => [
                    'direct' => ':attribute est en :year',
                    'inverse' => ':attribute n\'est pas en :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Date',
                ],

                'month' => [
                    'label' => 'Mois',
                ],

                'year' => [
                    'label' => 'Année',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Egal',
                    'inverse' => 'N\'est pas égal',
                ],

                'summary' => [
                    'direct' => ':attribute égal :number',
                    'inverse' => ':attribute n\'est pas égal :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Est maximum',
                    'inverse' => 'Est supérieur à',
                ],

                'summary' => [
                    'direct' => ':attribute est maximum :number',
                    'inverse' => ':attribute est supérieur à :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Est minimum',
                    'inverse' => 'Est inférieur à',
                ],

                'summary' => [
                    'direct' => ':attribute est minimum :number',
                    'inverse' => ':attribute est inférieur à :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Moyenne',
                    'summary' => 'Moyenne :attribute',
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
                    'label' => 'Somme',
                    'summary' => 'Somme de :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agrégat',
                ],

                'number' => [
                    'label' => 'Nombre',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'A',
                    'inverse' => 'N\'a pas',
                ],

                'summary' => [
                    'direct' => 'A :count :relationship',
                    'inverse' => 'N\'a pas :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'A un maximum',
                    'inverse' => 'A plus que',
                ],

                'summary' => [
                    'direct' => 'A un maximum :count :relationship',
                    'inverse' => 'A plus que :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'A un minimum',
                    'inverse' => 'A moins de',
                ],

                'summary' => [
                    'direct' => 'A un minimum :count :relationship',
                    'inverse' => 'A moins de :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Est vide',
                    'inverse' => 'N\'est pas vide',
                ],

                'summary' => [
                    'direct' => ':relationship est vide',
                    'inverse' => ':relationship n\'est pas vide',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Est',
                        'inverse' => 'N\'est pas',
                    ],

                    'multiple' => [
                        'direct' => 'Contient',
                        'inverse' => 'Ne contient pas',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship est :values',
                        'inverse' => ':relationship n\'est pas :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship contient :values',
                        'inverse' => ':relationship ne contient pas :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' or ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Valeur',
                    ],

                    'values' => [
                        'label' => 'Valeurs',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Compter',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Est',
                    'inverse' => 'N\'est pas',
                ],

                'summary' => [
                    'direct' => ':attribute est :values',
                    'inverse' => ':attribute n\'est pas :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' or ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Valeur',
                    ],

                    'values' => [
                        'label' => 'Valeurs',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Contient',
                    'inverse' => 'Ne contient pas',
                ],

                'summary' => [
                    'direct' => ':attribute contient :text',
                    'inverse' => ':attribute ne contient pas :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Se termine par',
                    'inverse' => 'Ne se termine pas par',
                ],

                'summary' => [
                    'direct' => ':attribute se termine par :text',
                    'inverse' => ':attribute ne se termine pas par :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Egal',
                    'inverse' => 'N\'est pas égal',
                ],

                'summary' => [
                    'direct' => ':attribute égal :text',
                    'inverse' => ':attribute n\'est pas égal :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Commence par',
                    'inverse' => 'Ne commence pas par',
                ],

                'summary' => [
                    'direct' => ':attribute commence par :text',
                    'inverse' => ':attribute ne commence pas par :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Texte',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Ajouter une règle',
        ],

        'add_rule_group' => [
            'label' => 'Ajouter une règle de groupe',
        ],

    ],

];
