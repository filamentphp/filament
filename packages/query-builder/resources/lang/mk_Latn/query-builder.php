<?php

return [

    'label' => 'Graditel na baranja',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Grupi',

            'block' => [
                'label' => 'ILI uslov',
                'or' => 'ILI',
            ],

        ],

        'rules' => [

            'label' => 'Pravila',

            'item' => [
                'and' => 'I',
            ],

        ],

    ],

    'no_rules' => '(Nema pravila)',

    'item_separators' => [
        'and' => 'I',
        'or' => 'ILI',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'E polneta',
                'inverse' => 'E prazna',
            ],

            'summary' => [
                'direct' => ':attribute e polneta',
                'inverse' => ':attribute e prazna',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'E točno',
                    'inverse' => 'E netočno',
                ],

                'summary' => [
                    'direct' => ':attribute e točno',
                    'inverse' => ':attribute e netočno',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'E posle',
                    'inverse' => 'Ne e posle',
                ],

                'summary' => [
                    'direct' => ':attribute e posle :date',
                    'inverse' => ':attribute ne e posle :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'E pred',
                    'inverse' => 'Ne e pred',
                ],

                'summary' => [
                    'direct' => ':attribute e pred :date',
                    'inverse' => ':attribute ne e pred :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'E datum',
                    'inverse' => 'Ne e datum',
                ],

                'summary' => [
                    'direct' => ':attribute e :date',
                    'inverse' => ':attribute ne e :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'E mesec',
                    'inverse' => 'Ne e mesec',
                ],

                'summary' => [
                    'direct' => ':attribute e :month',
                    'inverse' => ':attribute ne e :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'E godina',
                    'inverse' => 'Ne e godina',
                ],

                'summary' => [
                    'direct' => ':attribute e :year',
                    'inverse' => ':attribute ne e :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Sekundi',
                'minute' => 'Minuti',
                'hour' => 'Časa',
                'day' => 'Denovi',
                'week' => 'Sedmici',
                'month' => 'Meseci',
                'quarter' => 'Tromesečja',
                'year' => 'Godini',
            ],

            'presets' => [
                'past_decade' => 'Izminata dekada',
                'past_5_years' => 'Izminati 5 godini',
                'past_2_years' => 'Izminati 2 godini',
                'past_year' => 'Izminata godina',
                'past_6_months' => 'Izminati 6 meseci',
                'past_quarter' => 'Izminato tromesečje',
                'past_month' => 'Izminat mesec',
                'past_2_weeks' => 'Izminati 2 sedmici',
                'past_week' => 'Izminata sedmica',
                'past_hour' => 'Izminat čas',
                'past_minute' => 'Izminata minuta',
                'this_decade' => 'Ovaa dekada',
                'this_year' => 'Ovaa godina',
                'this_quarter' => 'Ova tromesečje',
                'this_month' => 'Ovoj mesec',
                'today' => 'Denes',
                'this_hour' => 'Ovoj čas',
                'this_minute' => 'Ovaa minuta',
                'next_minute' => 'Sledna minuta',
                'next_hour' => 'Sleden čas',
                'next_week' => 'Sledna sedmica',
                'next_2_weeks' => 'Sledni 2 sedmici',
                'next_month' => 'Sleden mesec',
                'next_quarter' => 'Sledno tromesečje',
                'next_6_months' => 'Sledni 6 meseci',
                'next_year' => 'Sledna godina',
                'next_2_years' => 'Sledni 2 godini',
                'next_5_years' => 'Sledni 5 godini',
                'next_decade' => 'Sledna dekada',
                'custom' => 'Prilagodeno',
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

                'mode' => [

                    'label' => 'Tip na datum',

                    'options' => [
                        'absolute' => 'Specifičen datum',
                        'relative' => 'Pomestuvački prozorec',
                    ],

                ],

                'preset' => [
                    'label' => 'Vremenski period',
                ],

                'relative_value' => [
                    'label' => 'Kolku',
                ],

                'relative_unit' => [
                    'label' => 'Vremenska edinica',
                ],

                'tense' => [

                    'label' => 'Vreme',

                    'options' => [
                        'past' => 'Minato',
                        'future' => 'Idnina',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Ednakvo',
                    'inverse' => 'Ne e ednakvo',
                ],

                'summary' => [
                    'direct' => ':attribute e ednakvo na :number',
                    'inverse' => ':attribute ne e ednakvo na :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'E maksimum',
                    'inverse' => 'E pogolemo od',
                ],

                'summary' => [
                    'direct' => ':attribute e maksimum :number',
                    'inverse' => ':attribute e pogolemo od :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'E minimum',
                    'inverse' => 'E pomalku od',
                ],

                'summary' => [
                    'direct' => ':attribute e minimum :number',
                    'inverse' => ':attribute e pomalku od :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Prosek',
                    'summary' => 'Prosek na :attribute',
                ],

                'max' => [
                    'label' => 'Maks',
                    'summary' => 'Maks :attribute',
                ],

                'min' => [
                    'label' => 'Min',
                    'summary' => 'Min :attribute',
                ],

                'sum' => [
                    'label' => 'Zbir',
                    'summary' => 'Zbir na :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregat',
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
                    'direct' => 'Ima maksimum',
                    'inverse' => 'Ima povekje od',
                ],

                'summary' => [
                    'direct' => 'Ima maksimum :count :relationship',
                    'inverse' => 'Ima povekje od :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Ima minimum',
                    'inverse' => 'Ima pomalku od',
                ],

                'summary' => [
                    'direct' => 'Ima minimum :count :relationship',
                    'inverse' => 'Ima pomalku od :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'E prazna',
                    'inverse' => 'Ne e prazna',
                ],

                'summary' => [
                    'direct' => ':relationship e prazna',
                    'inverse' => ':relationship ne e prazna',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'E',
                        'inverse' => 'Ne e',
                    ],

                    'multiple' => [
                        'direct' => 'Sodrži',
                        'inverse' => 'Ne sodrži',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship e :values',
                        'inverse' => ':relationship ne e :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship sodrži :values',
                        'inverse' => ':relationship ne sodrži :values',
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
                    'direct' => 'E',
                    'inverse' => 'Ne e',
                ],

                'summary' => [
                    'direct' => ':attribute e :values',
                    'inverse' => ':attribute ne e :values',
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
                    'direct' => 'Sodrži',
                    'inverse' => 'Ne sodrži',
                ],

                'summary' => [
                    'direct' => ':attribute sodrži :text',
                    'inverse' => ':attribute ne sodrži :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Završuva so',
                    'inverse' => 'Ne završuva so',
                ],

                'summary' => [
                    'direct' => ':attribute završuva so :text',
                    'inverse' => ':attribute ne završuva so :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Ednakvo',
                    'inverse' => 'Ne e ednakvo',
                ],

                'summary' => [
                    'direct' => ':attribute e ednakvo na :text',
                    'inverse' => ':attribute ne e ednakvo na :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Započnuva so',
                    'inverse' => 'Ne započnuva so',
                ],

                'summary' => [
                    'direct' => ':attribute započnuva so :text',
                    'inverse' => ':attribute ne započnuva so :text',
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
            'label' => 'Dodadi pravilo',
        ],

        'add_rule_group' => [
            'label' => 'Dodadi ILI',
        ],

    ],

];
