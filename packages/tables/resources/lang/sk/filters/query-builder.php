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
                'label' => 'Disjunkcia (ALEBO)',
                'or' => 'ALEBO',
            ],

        ],

        'rules' => [

            'label' => 'Pravidlá',

            'item' => [
                'and' => 'A',
            ],

        ],

    ],

    'no_rules' => '(Žiadne pravidlá)',

    'item_separators' => [
        'and' => 'A',
        'or' => 'ALEBO',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Je vyplnené',
                'inverse' => 'Je prázdne',
            ],

            'summary' => [
                'direct' => ':attribute je vyplnené',
                'inverse' => ':attribute je prázdne',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Je pravda',
                    'inverse' => 'Nie je pravda',
                ],

                'summary' => [
                    'direct' => ':attribute je pravda',
                    'inverse' => ':attribute nie je pravda',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Je po',
                    'inverse' => 'Nie je po',
                ],

                'summary' => [
                    'direct' => ':attribute je po :date',
                    'inverse' => ':attribute nie je po :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Je pred',
                    'inverse' => 'Nie je pred',
                ],

                'summary' => [
                    'direct' => ':attribute je pred :date',
                    'inverse' => ':attribute nie je pred :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Je dátum',
                    'inverse' => 'Nie je dátum',
                ],

                'summary' => [
                    'direct' => ':attribute je :date',
                    'inverse' => ':attribute nie je :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Je mesiac',
                    'inverse' => 'Nie je mesiac',
                ],

                'summary' => [
                    'direct' => ':attribute je :month',
                    'inverse' => ':attribute nie je :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Je rok',
                    'inverse' => 'Nie je rok',
                ],

                'summary' => [
                    'direct' => ':attribute je :year',
                    'inverse' => ':attribute nie je :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Dátum',
                ],

                'month' => [
                    'label' => 'Mesiac',
                ],

                'year' => [
                    'label' => 'Rok',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Rovná sa',
                    'inverse' => 'Nerovná sa',
                ],

                'summary' => [
                    'direct' => ':attribute sa rovná :number',
                    'inverse' => ':attribute sa nerovná :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Je maximálny',
                    'inverse' => 'Je väčší ako',
                ],

                'summary' => [
                    'direct' => ':attribute je maximálne :number',
                    'inverse' => ':attribute je väčší ako :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Je minimálny',
                    'inverse' => 'Je menšie ako',
                ],

                'summary' => [
                    'direct' => ':attribute je minimálne :number',
                    'inverse' => ':attribute je menšie ako :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Priemer',
                    'summary' => 'Priemer :attribute',
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
                    'summary' => 'Súčet :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregácia',
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
                    'direct' => 'Má maximálne',
                    'inverse' => 'Má viac ako',
                ],

                'summary' => [
                    'direct' => 'Má maximálne :count :relationship',
                    'inverse' => 'Má viac ako :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Má minimálne',
                    'inverse' => 'Má menej ako',
                ],

                'summary' => [
                    'direct' => 'Má minimálne :count :relationship',
                    'inverse' => 'Má menej ako :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Je prázdne',
                    'inverse' => 'Nie je prázdne',
                ],

                'summary' => [
                    'direct' => ':relationship je prázdne',
                    'inverse' => ':relationship nie je prázdne',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Je',
                        'inverse' => 'Nie je',
                    ],

                    'multiple' => [
                        'direct' => 'Obsahuje',
                        'inverse' => 'Neobsahuje',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship je :values',
                        'inverse' => ':relationship nie je :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship obsahuje :values',
                        'inverse' => ':relationship neobsahuje :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' alebo ',
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
                    'inverse' => 'Nie je',
                ],

                'summary' => [
                    'direct' => ':attribute je :values',
                    'inverse' => ':attribute nie je :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' alebo ',
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
                    'direct' => 'Rovná sa',
                    'inverse' => 'Nerovná sa',
                ],

                'summary' => [
                    'direct' => ':attribute sa rovná :text',
                    'inverse' => ':attribute sa nerovná :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Začína na',
                    'inverse' => 'Nezačína na',
                ],

                'summary' => [
                    'direct' => ':attribute začína na :text',
                    'inverse' => ':attribute nezačína na :text',
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
            'label' => 'Pridať pravidlo',
        ],

        'add_rule_group' => [
            'label' => 'Pridať skupinu pravidiel',
        ],

    ],

];
