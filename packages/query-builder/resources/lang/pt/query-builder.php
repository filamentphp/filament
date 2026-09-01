<?php

return [

    'label' => 'Constructor de Consultas',

    'form' => [

        'operator' => [
            'label' => 'Operador',
        ],

        'or_groups' => [

            'label' => 'Grupos',

            'block' => [
                'label' => 'Disjunção (OU)',
                'or' => 'OU',
            ],

        ],

        'rules' => [

            'label' => 'Regras',

            'item' => [
                'and' => 'E',
            ],

        ],

    ],

    'no_rules' => '(Sem regras)',

    'item_separators' => [
        'and' => 'E',
        'or' => 'OU',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Preenchido',
                'inverse' => 'Em branco',
            ],

            'summary' => [
                'direct' => ':attribute preenchido',
                'inverse' => ':attribute em branco',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'É verdadeiro',
                    'inverse' => 'É falso',
                ],

                'summary' => [
                    'direct' => ':attribute é verdadeiro',
                    'inverse' => ':attribute é falso',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'É posterior',
                    'inverse' => 'Não é posterior',
                ],

                'summary' => [
                    'direct' => ':attribute é posterior a :date',
                    'inverse' => ':attribute não é posterir a :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'É anterior',
                    'inverse' => 'Não é anterior',
                ],

                'summary' => [
                    'direct' => ':attribute é anterior a :date',
                    'inverse' => ':attribute não é anterior a :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'É data',
                    'inverse' => 'Não é data',
                ],

                'summary' => [
                    'direct' => ':attribute é :date',
                    'inverse' => ':attribute não é :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'É mês',
                    'inverse' => 'Não é mês',
                ],

                'summary' => [
                    'direct' => ':attribute é :month',
                    'inverse' => ':attribute não é :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'É ano',
                    'inverse' => 'Não é ano',
                ],

                'summary' => [
                    'direct' => ':attribute é :year',
                    'inverse' => ':attribute não é :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Data',
                ],

                'month' => [
                    'label' => 'Mês',
                ],

                'year' => [
                    'label' => 'Ano',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'É igual',
                    'inverse' => 'É diferente',
                ],

                'summary' => [
                    'direct' => ':attribute é igual a :number',
                    'inverse' => ':attribute é diferente de :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'É máximo',
                    'inverse' => 'É maior que',
                ],

                'summary' => [
                    'direct' => ':attribute é máximo :number',
                    'inverse' => ':attribute é maior que :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'É mínimo',
                    'inverse' => 'É menor que',
                ],

                'summary' => [
                    'direct' => ':attribute é mínimo :number',
                    'inverse' => ':attribute é menor que :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Média',
                    'summary' => 'Média de :attribute',
                ],

                'max' => [
                    'label' => 'Máx.',
                    'summary' => 'Máx. de :attribute',
                ],

                'min' => [
                    'label' => 'Mín.',
                    'summary' => 'Mín. de :attribute',
                ],

                'sum' => [
                    'label' => 'Soma',
                    'summary' => 'Soma de :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregação',
                ],

                'number' => [
                    'label' => 'Número',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Contém',
                    'inverse' => 'Não contém',
                ],

                'summary' => [
                    'direct' => 'Tem :count :relationship',
                    'inverse' => 'Não tem :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Tem máximo',
                    'inverse' => 'Tem mais de',
                ],

                'summary' => [
                    'direct' => 'Tem máximo de :count :relationship',
                    'inverse' => 'Tem mais de :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Tem mínimo',
                    'inverse' => 'Tem menos de',
                ],

                'summary' => [
                    'direct' => 'Tem mínimo de :count :relationship',
                    'inverse' => 'Tem emnos de :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'É vazio',
                    'inverse' => 'Não é vazio',
                ],

                'summary' => [
                    'direct' => ':relationship é vazio',
                    'inverse' => ':relationship não é vazio',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'É',
                        'inverse' => 'Não é',
                    ],

                    'multiple' => [
                        'direct' => 'Contém',
                        'inverse' => 'Não contém',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship é :values',
                        'inverse' => ':relationship não é :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship contém :values',
                        'inverse' => ':relationship não contém :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' ou ',
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
                    'label' => 'Contagem',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'É',
                    'inverse' => 'Não é',
                ],

                'summary' => [
                    'direct' => ':attribute é :values',
                    'inverse' => ':attribute não é :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' ou ',
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
                    'direct' => 'Contém',
                    'inverse' => 'Não contém',
                ],

                'summary' => [
                    'direct' => ':attribute contém :text',
                    'inverse' => ':attribute não contém :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Termina em',
                    'inverse' => 'Não termina em',
                ],

                'summary' => [
                    'direct' => ':attribute termina em :text',
                    'inverse' => ':attribute não termina em :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'É igual',
                    'inverse' => 'Não é igual',
                ],

                'summary' => [
                    'direct' => ':attribute é igual :text',
                    'inverse' => ':attribute não é igual :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Inicia por',
                    'inverse' => 'Não inicia por',
                ],

                'summary' => [
                    'direct' => ':attribute inicia por :text',
                    'inverse' => ':attribute não inicia por :text',
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
            'label' => 'Adicionar regra',
        ],

        'add_rule_group' => [
            'label' => 'Adicionar grupo de regras',
        ],

    ],

];
