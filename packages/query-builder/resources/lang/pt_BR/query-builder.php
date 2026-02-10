<?php

return [

    'label' => 'Query Builder',

    'form' => [

        'operator' => [
            'label' => 'Operador',
        ],

        'or_groups' => [

            'label' => 'Grupos',

            'block' => [
                'label' => 'Condição OU',
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
                'direct' => 'Está preenchido',
                'inverse' => 'Está vazio',
            ],

            'summary' => [
                'direct' => ':attribute está preenchido',
                'inverse' => ':attribute está vazio',
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
                    'inverse' => ':attribute não é posterior a :date',
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
                    'direct' => 'É a data',
                    'inverse' => 'Não é a data',
                ],

                'summary' => [
                    'direct' => ':attribute é :date',
                    'inverse' => ':attribute não é :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'É o mês',
                    'inverse' => 'Não é o mês',
                ],

                'summary' => [
                    'direct' => ':attribute é :month',
                    'inverse' => ':attribute não é :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'É o ano',
                    'inverse' => 'Não é o ano',
                ],

                'summary' => [
                    'direct' => ':attribute é :year',
                    'inverse' => ':attribute não é :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Segundos',
                'minute' => 'Minutos',
                'hour' => 'Horas',
                'day' => 'Dias',
                'week' => 'Semanas',
                'month' => 'Meses',
                'quarter' => 'Trimestres',
                'year' => 'Anos',
            ],

            'presets' => [
                'past_decade' => 'Última década',
                'past_5_years' => 'Últimos 5 anos',
                'past_2_years' => 'Últimos 2 anos',
                'past_year' => 'Último ano',
                'past_6_months' => 'Últimos 6 meses',
                'past_quarter' => 'Último trimestre',
                'past_month' => 'Último mês',
                'past_2_weeks' => 'Últimas 2 semanas',
                'past_week' => 'Última semana',
                'past_hour' => 'Última hora',
                'past_minute' => 'Último minuto',
                'this_decade' => 'Esta década',
                'this_year' => 'Este ano',
                'this_quarter' => 'Este trimestre',
                'this_month' => 'Este mês',
                'today' => 'Hoje',
                'this_hour' => 'Esta hora',
                'this_minute' => 'Este minuto',
                'next_minute' => 'Próximo minuto',
                'next_hour' => 'Próxima hora',
                'next_week' => 'Próxima semana',
                'next_2_weeks' => 'Próximas 2 semanas',
                'next_month' => 'Próximo mês',
                'next_quarter' => 'Próximo trimestre',
                'next_6_months' => 'Próximos 6 meses',
                'next_year' => 'Próximo ano',
                'next_2_years' => 'Próximos 2 anos',
                'next_5_years' => 'Próximos 5 anos',
                'next_decade' => 'Próxima década',
                'custom' => 'Personalizado',
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

                'mode' => [

                    'label' => 'Tipo',

                    'options' => [
                        'absolute' => 'Data específica',
                        'relative' => 'Período',
                    ],

                ],

                'preset' => [
                    'label' => 'Período',
                ],

                'relative_value' => [
                    'label' => 'Quantidade',
                ],

                'relative_unit' => [
                    'label' => 'Unidade de tempo',
                ],

                'tense' => [

                    'label' => 'Direção',

                    'options' => [
                        'past' => 'Passado',
                        'future' => 'Futuro',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'É igual a',
                    'inverse' => 'Diferente de',
                ],

                'summary' => [
                    'direct' => ':attribute é igual a :number',
                    'inverse' => ':attribute diferente de :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'É no máximo',
                    'inverse' => 'É maior que',
                ],

                'summary' => [
                    'direct' => ':attribute é no máximo :number',
                    'inverse' => ':attribute é maior que :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'É no mínimo',
                    'inverse' => 'É menor que',
                ],

                'summary' => [
                    'direct' => ':attribute é no mínimo :number',
                    'inverse' => ':attribute é menor que :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Média',
                    'summary' => 'Média de :attribute',
                ],

                'max' => [
                    'label' => 'Máximo',
                    'summary' => 'Máximo de :attribute',
                ],

                'min' => [
                    'label' => 'Mínimo',
                    'summary' => 'Mínimo de :attribute',
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
                    'direct' => 'Tem',
                    'inverse' => 'Não tem',
                ],

                'summary' => [
                    'direct' => 'Tem :count :relationship',
                    'inverse' => 'Não tem :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Tem no máximo',
                    'inverse' => 'Tem mais que',
                ],

                'summary' => [
                    'direct' => 'Tem no máximo :count :relationship',
                    'inverse' => 'Tem mais que :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Tem no mínimo',
                    'inverse' => 'Tem menos que',
                ],

                'summary' => [
                    'direct' => 'Tem no mínimo :count :relationship',
                    'inverse' => 'Tem menos que :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Está vazio',
                    'inverse' => 'Não está vazio',
                ],

                'summary' => [
                    'direct' => ':relationship está vazio',
                    'inverse' => ':relationship não está vazio',
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
                    'label' => 'Quantidade',
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
                    'direct' => 'Termina com',
                    'inverse' => 'Não termina com',
                ],

                'summary' => [
                    'direct' => ':attribute termina com :text',
                    'inverse' => ':attribute não termina com :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'É igual a',
                    'inverse' => 'Diferente de',
                ],

                'summary' => [
                    'direct' => ':attribute é igual a :text',
                    'inverse' => ':attribute diferente de :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Começa com',
                    'inverse' => 'Não começa com',
                ],

                'summary' => [
                    'direct' => ':attribute começa com :text',
                    'inverse' => ':attribute não começa com :text',
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
            'label' => 'Adicionar OU',
        ],

    ],

];
