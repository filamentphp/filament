<?php

return [

    'label' => 'Generatore di query',

    'form' => [

        'operator' => [
            'label' => 'Operatore',
        ],

        'or_groups' => [

            'label' => 'Gruppi',

            'block' => [
                'label' => 'Separatore (OR)',
                'or' => 'OR',
            ],

        ],

        'rules' => [

            'label' => 'Regole',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(Nessuna regola)',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'È pieno',
                'inverse' => 'È vuoto',
            ],

            'summary' => [
                'direct' => ':attribute è pieno',
                'inverse' => ':attribute è vuoto',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'È vero',
                    'inverse' => 'È falso',
                ],

                'summary' => [
                    'direct' => ':attribute è vero',
                    'inverse' => ':attribute è falso',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'È dopo',
                    'inverse' => 'Non è dopo',
                ],

                'summary' => [
                    'direct' => ':attribute è dopo :date',
                    'inverse' => ':attribute non è dopo :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'È prima',
                    'inverse' => 'Non è prima',
                ],

                'summary' => [
                    'direct' => ':attribute è prima :date',
                    'inverse' => ':attribute non è prima :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'È data',
                    'inverse' => 'Non è data',
                ],

                'summary' => [
                    'direct' => ':attribute è :date',
                    'inverse' => ':attribute non è :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'È mese',
                    'inverse' => 'Non è mese',
                ],

                'summary' => [
                    'direct' => ':attribute è :month',
                    'inverse' => ':attribute non è :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'È anno',
                    'inverse' => 'Non è anno',
                ],

                'summary' => [
                    'direct' => ':attribute è :year',
                    'inverse' => ':attribute non è :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Data',
                ],

                'month' => [
                    'label' => 'Mese',
                ],

                'year' => [
                    'label' => 'Anno',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Equivale',
                    'inverse' => 'Non equivale',
                ],

                'summary' => [
                    'direct' => ':attribute equivale a :number',
                    'inverse' => ':attribute non equivale a :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'È massimo',
                    'inverse' => 'È più grandi di',
                ],

                'summary' => [
                    'direct' => ':attribute è massimo :number',
                    'inverse' => ':attribute è più grande di :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'È minimo',
                    'inverse' => 'È meno di',
                ],

                'summary' => [
                    'direct' => ':attribute è minimo :number',
                    'inverse' => ':attribute è meno di :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Media',
                    'summary' => 'Media :attribute',
                ],

                'max' => [
                    'label' => 'Massimo',
                    'summary' => 'Massimo :attribute',
                ],

                'min' => [
                    'label' => 'Minimo',
                    'summary' => 'Minimo :attribute',
                ],

                'sum' => [
                    'label' => 'Somma',
                    'summary' => 'Somma di :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Aggregata',
                ],

                'number' => [
                    'label' => 'Numero',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Ha',
                    'inverse' => 'Non ha',
                ],

                'summary' => [
                    'direct' => 'Ha :count :relationship',
                    'inverse' => 'Non ha :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Ha massimo',
                    'inverse' => 'Ha più di',
                ],

                'summary' => [
                    'direct' => 'Ha minimo :count :relationship',
                    'inverse' => 'Ha più di :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Ha minimo',
                    'inverse' => 'Ha meno di',
                ],

                'summary' => [
                    'direct' => 'Ha minimo :count :relationship',
                    'inverse' => 'Ha meno di :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'È vuoto',
                    'inverse' => 'Non è vuoto',
                ],

                'summary' => [
                    'direct' => ':relationship è vuoto',
                    'inverse' => ':relationship non è vuoto',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'È',
                        'inverse' => 'Non è',
                    ],

                    'multiple' => [
                        'direct' => 'Contiene',
                        'inverse' => 'Non contiene',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship è :values',
                        'inverse' => ':relationship non è :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship contiene :values',
                        'inverse' => ':relationship non conviene :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' oppure ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Valore',
                    ],

                    'values' => [
                        'label' => 'Valori',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Conteggio',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'È',
                    'inverse' => 'Non è',
                ],

                'summary' => [
                    'direct' => ':attribute è :values',
                    'inverse' => ':attribute non è :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' oppure ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Valore',
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
                    'direct' => 'Contiene',
                    'inverse' => 'Non contiene',
                ],

                'summary' => [
                    'direct' => ':attribute contiene :text',
                    'inverse' => ':attribute non contiene :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Finisce con',
                    'inverse' => 'Non finisce con',
                ],

                'summary' => [
                    'direct' => ':attribute finisce con :text',
                    'inverse' => ':attribute non finisce con :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Uguale',
                    'inverse' => 'Non è uguale',
                ],

                'summary' => [
                    'direct' => ':attribute uguale a :text',
                    'inverse' => ':attribute non è uguale a :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Inizia con',
                    'inverse' => 'Non inizia con',
                ],

                'summary' => [
                    'direct' => ':attribute inizia con :text',
                    'inverse' => ':attribute non inizia con :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Testo',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Aggiungi regola',
        ],

        'add_rule_group' => [
            'label' => 'Aggiungi gruppo di regole',
        ],

    ],

];
