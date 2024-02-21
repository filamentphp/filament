<?php

return [

    'label' => 'Abfragegenerator',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Gruppen',

            'block' => [
                'label' => '(ODER) Trennung',
                'or' => 'ODER',
            ],

        ],

        'rules' => [

            'label' => 'Bedingungen',

            'item' => [
                'and' => 'UND',
            ],

        ],

    ],

    'no_rules' => '(Keine Bedinungen)',

    'item_separators' => [
        'and' => 'UND',
        'or' => 'ODER',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'ausgefüllt',
                'inverse' => 'leer',
            ],

            'summary' => [
                'direct' => ':Attribut ist vorhangen',
                'inverse' => ':Attribut ist nicht vorhanden',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'ist wahr',
                    'inverse' => 'ist falsch',
                ],

                'summary' => [
                    'direct' => ':attribute ist wahr',
                    'inverse' => ':attribute ist falsch',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'nach',
                    'inverse' => 'vor',
                ],

                'summary' => [
                    'direct' => ':attribute is after :date',
                    'inverse' => ':attribute is not after :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'ist vor',
                    'inverse' => 'ist nach',
                ],

                'summary' => [
                    'direct' => ':attribute ist vor :date',
                    'inverse' => ':attribute ist nicht vor :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'ist Datum',
                    'inverse' => 'ist kein Datum',
                ],

                'summary' => [
                    'direct' => ':attribute ist :date',
                    'inverse' => ':attribute ist kein :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'ist ein ',
                    'inverse' => 'ist kein Monat',
                ],

                'summary' => [
                    'direct' => ':attribute ist :month',
                    'inverse' => ':attribute ist kein :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'ist ein Jahr',
                    'inverse' => 'ist kein Jahr',
                ],

                'summary' => [
                    'direct' => ':attribute ist ein :year',
                    'inverse' => ':attribute ist kein :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Datum',
                ],

                'month' => [
                    'label' => 'Monat',
                ],

                'year' => [
                    'label' => 'Jahr',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'ist gleich',
                    'inverse' => 'ist nicht gleich',
                ],

                'summary' => [
                    'direct' => ':attribute ist gleich :number',
                    'inverse' => ':attribute ist nicht gleich :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Maximal',
                    'inverse' => 'Größer als',
                ],

                'summary' => [
                    'direct' => ':attribute ist maximal :number',
                    'inverse' => ':attribute ist größer als :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Minimum',
                    'inverse' => 'ist kleiner als',
                ],

                'summary' => [
                    'direct' => ':attribute ist Minimum von :number',
                    'inverse' => ':attribute ist kleiner als :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Durchschnitt',
                    'summary' => 'Durchschnittliches :attribute',
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
                    'label' => 'Gesamt',
                    'summary' => 'Gesamt von :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Verknüpfung',
                ],

                'number' => [
                    'label' => 'Zahl',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Hat',
                    'inverse' => 'Hat nicht',
                ],

                'summary' => [
                    'direct' => 'Hat :count :relationship',
                    'inverse' => 'Hat nicht :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Hat Maximal',
                    'inverse' => 'Hat mehr als',
                ],

                'summary' => [
                    'direct' => 'Hat maximal :count :relationship',
                    'inverse' => 'Hat mehr als :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Hat mindestens',
                    'inverse' => 'Hat weniger als',
                ],

                'summary' => [
                    'direct' => 'Hat mindestens :count :relationship',
                    'inverse' => 'Hat weniger als:count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'ist leer',
                    'inverse' => 'ist nicht leer',
                ],

                'summary' => [
                    'direct' => ':relationship ist leer',
                    'inverse' => ':relationship ist nicht leer',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'ist',
                        'inverse' => 'ist nicht',
                    ],

                    'multiple' => [
                        'direct' => 'Beinhaltet',
                        'inverse' => 'Beinhaltet nicht',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship ist :values',
                        'inverse' => ':relationship ist nicht :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship beinhaltet :values',
                        'inverse' => ':relationship beinhaltet nicht :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' oder ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Wert',
                    ],

                    'values' => [
                        'label' => 'Werte',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Anzahl',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'ist',
                    'inverse' => 'ist nicht',
                ],

                'summary' => [
                    'direct' => ':attribute ist :values',
                    'inverse' => ':attribute ist nicht :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' oder ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Wert',
                    ],

                    'values' => [
                        'label' => 'Werte',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Beinhaltet',
                    'inverse' => 'Beinhaltet nicht',
                ],

                'summary' => [
                    'direct' => ':attribute beinhaltet :text',
                    'inverse' => ':attribute beinhaltet nicht :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Endet mit',
                    'inverse' => 'Endet nicht mit',
                ],

                'summary' => [
                    'direct' => ':attribute endet mit :text',
                    'inverse' => ':attribute endet nicht mit :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Ist gleich',
                    'inverse' => 'Ist nicht gleich',
                ],

                'summary' => [
                    'direct' => ':attribute ist gleich :text',
                    'inverse' => ':attribute ist nicht gleich :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Beginnt mit',
                    'inverse' => 'Beginnt nicht mit',
                ],

                'summary' => [
                    'direct' => ':attribute beginnt mit :text',
                    'inverse' => ':attribute beginnt nicht mit :text',
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
            'label' => 'Bedingung hinzufügen',
        ],

        'add_rule_group' => [
            'label' => 'Bedingungsgruppe hinzufügen',
        ],

    ],

];
