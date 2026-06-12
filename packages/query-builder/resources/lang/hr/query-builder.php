<?php

return [
    'label' => 'Izrada upita',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Grupa',

            'block' => [
                'label' => 'Disjunkcija (OR)',
                'or' => 'OR',
            ],

        ],

        'rules' => [

            'label' => 'Pravila',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(No rules)',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Ima vrijednost',
                'inverse' => 'nema vrijednost',
            ],

            'summary' => [
                'direct' => ':attribute ima vrijednost',
                'inverse' => ':attribute nema vrijednost',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Valjano',
                    'inverse' => 'Nije valjano',
                ],

                'summary' => [
                    'direct' => ':attribute je valjan',
                    'inverse' => ':attribute nije valjan',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Dolazi poslije',
                    'inverse' => 'Ne dolazi poslije',
                ],

                'summary' => [
                    'direct' => ':attribute dolazi poslije :date',
                    'inverse' => ':attribute ine dolazi poslije :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Dolazi prije',
                    'inverse' => 'Ne dolazi prije',
                ],

                'summary' => [
                    'direct' => ':attribute dolazi prije :date',
                    'inverse' => ':attribute ne dolazi prije :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Datum',
                    'inverse' => 'Nije datum',
                ],

                'summary' => [
                    'direct' => ':attribute je datum :date',
                    'inverse' => ':attribute nije datum :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Mjesec',
                    'inverse' => 'Nije mjesec',
                ],

                'summary' => [
                    'direct' => ':attribute je :month',
                    'inverse' => ':attribute nije :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Godina',
                    'inverse' => 'Nije godina',
                ],

                'summary' => [
                    'direct' => ':attribute je :year',
                    'inverse' => ':attribute nije :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Datum',
                ],

                'month' => [
                    'label' => 'Mjesec',
                ],

                'year' => [
                    'label' => 'Godina',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Jednako',
                    'inverse' => 'Nije jednako',
                ],

                'summary' => [
                    'direct' => ':attribute equals :number',
                    'inverse' => ':attribute does not equal :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Jeste najveći',
                    'inverse' => 'Veći je od',
                ],

                'summary' => [
                    'direct' => ':attribute is maximum :number',
                    'inverse' => ':attribute is greater than :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Jeste najmanji',
                    'inverse' => 'Manji je od',
                ],

                'summary' => [
                    'direct' => ':attribute is minimum :number',
                    'inverse' => ':attribute is less than :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Prosječan',
                    'summary' => 'Average :attribute',
                ],

                'max' => [
                    'label' => 'Najveći',
                    'summary' => 'Max :attribute',
                ],

                'min' => [
                    'label' => 'Najmanji',
                    'summary' => 'Min :attribute',
                ],

                'sum' => [
                    'label' => 'Zbroj',
                    'summary' => 'Sum of :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregirano',
                ],

                'number' => [
                    'label' => 'Broj',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Ima',
                    'inverse' => 'Nema',
                ],

                'summary' => [
                    'direct' => 'Has :count :relationship',
                    'inverse' => 'Does not have :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Ima najviše',
                    'inverse' => 'Ima više od',
                ],

                'summary' => [
                    'direct' => 'Has maximum :count :relationship',
                    'inverse' => 'Has more than :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Ima najmanje',
                    'inverse' => 'Ima manje od',
                ],

                'summary' => [
                    'direct' => 'Has minimum :count :relationship',
                    'inverse' => 'Has less than :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Ne sadrži podatke',
                    'inverse' => 'Sadrži podatke',
                ],

                'summary' => [
                    'direct' => ':relationship is empty',
                    'inverse' => ':relationship is not empty',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Jeste',
                        'inverse' => 'Nije',
                    ],

                    'multiple' => [
                        'direct' => 'Sadrži',
                        'inverse' => 'Ne sadrži',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship is :values',
                        'inverse' => ':relationship is not :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship contains :values',
                        'inverse' => ':relationship does not contain :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' or ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Vrijednost',
                    ],

                    'values' => [
                        'label' => 'Vrijednosti',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Broj',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Je',
                    'inverse' => 'Nije',
                ],

                'summary' => [
                    'direct' => ':attribute is :values',
                    'inverse' => ':attribute is not :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' or ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Vrijednost',
                    ],

                    'values' => [
                        'label' => 'Vrijednosti',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Sadrži',
                    'inverse' => 'Ne sadrži',
                ],

                'summary' => [
                    'direct' => ':attribute contains :text',
                    'inverse' => ':attribute does not contain :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Završava s',
                    'inverse' => 'Ne završava s',
                ],

                'summary' => [
                    'direct' => ':attribute ends with :text',
                    'inverse' => ':attribute does not end with :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Jednako',
                    'inverse' => 'Nije jednako',
                ],

                'summary' => [
                    'direct' => ':attribute equals :text',
                    'inverse' => ':attribute does not equal :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Počinje s',
                    'inverse' => 'Ne počinje s',
                ],

                'summary' => [
                    'direct' => ':attribute starts with :text',
                    'inverse' => ':attribute does not start with :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Tekst',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Dodaj pravilo',
        ],

        'add_rule_group' => [
            'label' => 'Dodaj grupu pravila',
        ],

    ],

];
