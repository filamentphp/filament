<?php

return [

    'label' => 'Graditelj poizvedb',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Skupine',

            'block' => [
                'label' => 'Disjunkcija (ALI)',
                'or' => 'ALI',
            ],

        ],

        'rules' => [

            'label' => 'Pravila',

            'item' => [
                'and' => 'IN',
            ],

        ],

    ],

    'no_rules' => '(Brez pravil)',

    'item_separators' => [
        'and' => 'IN',
        'or' => 'ALI',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Je izpolnjeno',
                'inverse' => 'Je prazno',
            ],

            'summary' => [
                'direct' => ':attribute je izpolnjeno',
                'inverse' => ':attribute je prazno',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Je res',
                    'inverse' => 'Ni res',
                ],

                'summary' => [
                    'direct' => ':attribute je res',
                    'inverse' => ':attribute ni res',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Je po',
                    'inverse' => 'Ni po',
                ],

                'summary' => [
                    'direct' => ':attribute je po :date',
                    'inverse' => ':attribute ni po :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Je pred',
                    'inverse' => 'Ni pred',
                ],

                'summary' => [
                    'direct' => ':attribute je pred :date',
                    'inverse' => ':attribute ni pred :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Je datum',
                    'inverse' => 'Ni datum',
                ],

                'summary' => [
                    'direct' => ':attribute je :date',
                    'inverse' => ':attribute ni :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Je mesec',
                    'inverse' => 'Ni mesec',
                ],

                'summary' => [
                    'direct' => ':attribute je :month',
                    'inverse' => ':attribute ni :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Je leto',
                    'inverse' => 'Ni leto',
                ],

                'summary' => [
                    'direct' => ':attribute je :year',
                    'inverse' => ':attribute ni :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Datum',
                ],

                'month' => [
                    'label' => 'Mesec',
                ],

                'year' => [
                    'label' => 'Leto',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Je enako',
                    'inverse' => 'Ni enako',
                ],

                'summary' => [
                    'direct' => ':attribute je enako :number',
                    'inverse' => ':attribute ni enako :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Je največje',
                    'inverse' => 'Je večje kot',
                ],

                'summary' => [
                    'direct' => ':attribute je največje :number',
                    'inverse' => ':attribute je večje kot :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Je najmanjše',
                    'inverse' => 'Je manjše kot',
                ],

                'summary' => [
                    'direct' => ':attribute je najmanjše :number',
                    'inverse' => ':attribute je manjše kot :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Povprečje',
                    'summary' => 'Povprečje :attribute',
                ],

                'max' => [
                    'label' => 'Največ',
                    'summary' => 'Največ :attribute',
                ],

                'min' => [
                    'label' => 'Najmanj',
                    'summary' => 'Najmanj :attribute',
                ],

                'sum' => [
                    'label' => 'Vsota',
                    'summary' => 'Vsota :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Skupni seštevek',
                ],

                'number' => [
                    'label' => 'Število',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Ima',
                    'inverse' => 'Nima',
                ],

                'summary' => [
                    'direct' => 'Ima :count :relationship',
                    'inverse' => 'Nima :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Ima največ',
                    'inverse' => 'Ima več kot',
                ],

                'summary' => [
                    'direct' => 'Ima največ :count :relationship',
                    'inverse' => 'Ima več kot :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Ima najmanj',
                    'inverse' => 'Ima manj kot',
                ],

                'summary' => [
                    'direct' => 'Ima najmanj :count :relationship',
                    'inverse' => 'Ima manj kot :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Je prazno',
                    'inverse' => 'Ni prazno',
                ],

                'summary' => [
                    'direct' => ':relationship je prazno',
                    'inverse' => ':relationship ni prazno',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Je',
                        'inverse' => 'Ni',
                    ],

                    'multiple' => [
                        'direct' => 'Vsebuje',
                        'inverse' => 'Ne vsebuje',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship je :values',
                        'inverse' => ':relationship ni :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship vsebuje :values',
                        'inverse' => ':relationship ne vsebuje :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' ali ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Vrednost',
                    ],

                    'values' => [
                        'label' => 'Vrednosti',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Število',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Je',
                    'inverse' => 'Ni',
                ],

                'summary' => [
                    'direct' => ':attribute je :values',
                    'inverse' => ':attribute ni :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' ali ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Vrednost',
                    ],

                    'values' => [
                        'label' => 'Vrednosti',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Vsebuje',
                    'inverse' => 'Ne vsebuje',
                ],

                'summary' => [
                    'direct' => ':attribute vsebuje :text',
                    'inverse' => ':attribute ne vsebuje :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Se konča z',
                    'inverse' => 'Se ne konča z',
                ],

                'summary' => [
                    'direct' => ':attribute se konča z :text',
                    'inverse' => ':attribute se ne konča z :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Je enako',
                    'inverse' => 'Ni enako',
                ],

                'summary' => [
                    'direct' => ':attribute je enako :text',
                    'inverse' => ':attribute ni enako :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Se začne z',
                    'inverse' => 'Se ne začne z',
                ],

                'summary' => [
                    'direct' => ':attribute se začne z :text',
                    'inverse' => ':attribute se ne začne z :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Besedilo',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Dodaj pravilo',
        ],

        'add_rule_group' => [
            'label' => 'Dodaj skupino pravil',
        ],

    ],

];
