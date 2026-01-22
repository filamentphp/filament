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
                'direct' => 'Compilato',
                'inverse' => 'Non compilato',
            ],

            'summary' => [
                'direct' => ':attribute è compilato',
                'inverse' => ':attribute non è compilato',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Vero',
                    'inverse' => 'Falso',
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
                    'direct' => 'Dopo il',
                    'inverse' => 'Fino al',
                ],

                'summary' => [
                    'direct' => ':attribute dopo il :date',
                    'inverse' => ':attribute fino al :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Prima del',
                    'inverse' => 'Non prima del',
                ],

                'summary' => [
                    'direct' => ':attribute è prima del :date',
                    'inverse' => ':attribute non è prima del :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Data uguale a',
                    'inverse' => 'Data diversa da',
                ],

                'summary' => [
                    'direct' => ':attribute è uguale a :date',
                    'inverse' => ':attribute è diversa da :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Mese uguale a',
                    'inverse' => 'Mese diverso da',
                ],

                'summary' => [
                    'direct' => ':attribute è uguale a :month',
                    'inverse' => ':attribute è diverso da :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Anno uguale a ',
                    'inverse' => 'Anno diverso da',
                ],

                'summary' => [
                    'direct' => ':attribute è uguale a :year',
                    'inverse' => ':attribute è diverso da :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Secondi',
                'minute' => 'Minuti',
                'hour' => 'Ore',
                'day' => 'Giorni',
                'week' => 'Settimane',
                'month' => 'Mesi',
                'quarter' => 'Trimestri',
                'year' => 'Anni',
            ],

            'presets' => [
                'past_decade' => 'Ultimo decennio',
                'past_5_years' => 'Ultimi 5 anni',
                'past_2_years' => 'Ultimi 2 anni',
                'past_year' => 'Ultimo anno',
                'past_6_months' => 'Ultimi 6 mesi',
                'past_quarter' => 'Ultimo trimestre',
                'past_month' => 'Ultimo mese',
                'past_2_weeks' => 'Ultime 2 settimane',
                'past_week' => 'Ultima settimana',
                'past_hour' => 'Ultima ora',
                'past_minute' => 'Ultimo minuto',
                'this_decade' => 'Questo decennio',
                'this_year' => "Quest'anno",
                'this_quarter' => 'Questo trimestre',
                'this_month' => 'Questo mese',
                'today' => 'Oggi',
                'this_hour' => "Quest'ora",
                'this_minute' => 'Questo minuto',
                'next_minute' => 'Prossimo minuto',
                'next_hour' => 'Prossima ora',
                'next_week' => 'Prossima settimana',
                'next_2_weeks' => 'Prossime 2 settimane',
                'next_month' => 'Prossimo mese',
                'next_quarter' => 'Prossimo trimestre',
                'next_6_months' => 'Prossimi 6 mesi',
                'next_year' => 'Prossimo anno',
                'next_2_years' => 'Prossimi 2 anni',
                'next_5_years' => 'Prossimi 5 anni',
                'next_decade' => 'Prossimo decennio',
                'custom' => 'Personalizzato',
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

                'mode' => [

                    'label' => 'Tipologia data',

                    'options' => [
                        'absolute' => 'Data specifica',
                        'relative' => 'Intervallo',
                    ],

                ],

                'preset' => [
                    'label' => 'Periodo di tempo',
                ],

                'relative_value' => [
                    'label' => 'Quanti',
                ],

                'relative_unit' => [
                    'label' => 'Unità di tempo',
                ],

                'tense' => [

                    'label' => 'Tempo',

                    'options' => [
                        'past' => 'Passato',
                        'future' => 'Futuro',
                    ],

                ],
            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Uguale a ',
                    'inverse' => 'Diverso da',
                ],

                'summary' => [
                    'direct' => ':attribute uguale a :number',
                    'inverse' => ':attribute diverso da :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Massimo',
                    'inverse' => 'Più grande di',
                ],

                'summary' => [
                    'direct' => ':attribute è massimo :number',
                    'inverse' => ':attribute è più grande di :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Minimo',
                    'inverse' => 'Meno di',
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
                    'direct' => 'Ha al massimo',
                    'inverse' => 'Ha più di',
                ],

                'summary' => [
                    'direct' => 'Ha al massimo :count :relationship',
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
                        'direct' => 'È uguale a',
                        'inverse' => 'È diverso da',
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
                        'inverse' => ':relationship non contiene :values',
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
                    'direct' => 'È uguale a',
                    'inverse' => 'È diverso da',
                ],

                'summary' => [
                    'direct' => ':attribute è uguale a :values',
                    'inverse' => ':attribute diverso da :values',
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
                    'direct' => 'Uguale a',
                    'inverse' => 'Diverso da',
                ],

                'summary' => [
                    'direct' => ':attribute uguale a :text',
                    'inverse' => ':attribute diversa da :text',
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
