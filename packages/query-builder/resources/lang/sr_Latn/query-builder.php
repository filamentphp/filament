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
                'label' => 'Ili (OR)',
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

    'no_rules' => '(Bez pravila)',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Ima vrednost',
                'inverse' => 'Nema vrednost',
            ],

            'summary' => [
                'direct' => ':attribute ima vrednost',
                'inverse' => ':attribute nema vrednost',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Ispravo',
                    'inverse' => 'Nije ispravno',
                ],

                'summary' => [
                    'direct' => ':attribute je ispravan',
                    'inverse' => ':attribute nije ispravan',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Dolazi posle',
                    'inverse' => 'Ne dolazi posle',
                ],

                'summary' => [
                    'direct' => ':attribute dolazi posle :date',
                    'inverse' => ':attribute ne dolazi posle :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Dolazi pre',
                    'inverse' => 'Ne dolazi pre',
                ],

                'summary' => [
                    'direct' => ':attribute dolazi pre :date',
                    'inverse' => ':attribute ne dolazi pre :date',
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
                    'direct' => 'Mesec',
                    'inverse' => 'Nije mesec',
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
                    'label' => 'Mesec',
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
                    'inverse' => 'Nejednako',
                ],

                'summary' => [
                    'direct' => ':attribute je :number',
                    'inverse' => ':attribute nije :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Najveći',
                    'inverse' => 'Veći je od',
                ],

                'summary' => [
                    'direct' => ':attribute je najveći :number',
                    'inverse' => ':attribute je veći od :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Najmanji',
                    'inverse' => 'Manji je od',
                ],

                'summary' => [
                    'direct' => ':attribute je najmanji :number',
                    'inverse' => ':attribute je manji od :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Prosečan',
                    'summary' => 'Prosek :attribute',
                ],

                'max' => [
                    'label' => 'Najveći',
                    'summary' => 'Najveći :attribute',
                ],

                'min' => [
                    'label' => 'Najmanji',
                    'summary' => 'Najmanji :attribute',
                ],

                'sum' => [
                    'label' => 'Zbir',
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
                    'direct' => 'Ima :count :relationship',
                    'inverse' => 'Nema :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Ima najviše',
                    'inverse' => 'Ima više od',
                ],

                'summary' => [
                    'direct' => 'Ima najviše :count :relationship',
                    'inverse' => 'Ima više od :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Ima najmanje',
                    'inverse' => 'Ima manje od',
                ],

                'summary' => [
                    'direct' => 'Ima najmanje :count :relationship',
                    'inverse' => 'Ima manje od :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Ne sadrži podatke',
                    'inverse' => 'Sadrži podatke',
                ],

                'summary' => [
                    'direct' => ':relationship je prazno',
                    'inverse' => ':relationship nije prazno',
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
                        'direct' => ':relationship je :values',
                        'inverse' => ':relationship nije :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship sadrži :values',
                        'inverse' => ':relationship ne sadrži :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' ili ',
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
                    'direct' => ':attribute je :values',
                    'inverse' => ':attribute nije :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' ili ',
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
                    'direct' => 'Sadrži',
                    'inverse' => 'Ne sadrži',
                ],

                'summary' => [
                    'direct' => ':attribute sadrži :text',
                    'inverse' => ':attribute ne sadrži :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Završava sa',
                    'inverse' => 'Ne završava sa',
                ],

                'summary' => [
                    'direct' => ':attribute završava sa :text',
                    'inverse' => ':attribute ne završava sa :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Jednako',
                    'inverse' => 'Različito',
                ],

                'summary' => [
                    'direct' => ':attribute isto kao :text',
                    'inverse' => ':attribute različito od :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Počinje sa',
                    'inverse' => 'Ne počinje sa',
                ],

                'summary' => [
                    'direct' => ':attribute počinje sa :text',
                    'inverse' => ':attribute ne počinje sa :text',
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
            'label' => 'Doda pravilo',
        ],

        'add_rule_group' => [
            'label' => 'Doda grupu pra',
        ],

    ],

];
