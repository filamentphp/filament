<?php

return [

    'label' => 'Geavanceerd filteren',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Groepen',

            'block' => [
                'label' => 'Disjunctie (OF)',
                'or' => 'OF',
            ],

        ],

        'rules' => [

            'label' => 'Regels',

            'item' => [
                'and' => 'EN',
            ],

        ],

    ],

    'no_rules' => '(Geen regels)',

    'item_separators' => [
        'and' => 'EN',
        'or' => 'OF',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Is gevuld',
                'inverse' => 'Is leeg',
            ],

            'summary' => [
                'direct' => ':attribute is gevuld',
                'inverse' => ':attribute is leeg',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Is waar',
                    'inverse' => 'Is onwaar',
                ],

                'summary' => [
                    'direct' => ':attribute is waar',
                    'inverse' => ':attribute is onwaar',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Is na',
                    'inverse' => 'Is niet na',
                ],

                'summary' => [
                    'direct' => ':attribute is na :date',
                    'inverse' => ':attribute is niet na :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Is voor',
                    'inverse' => 'Is niet voor',
                ],

                'summary' => [
                    'direct' => ':attribute is voor :date',
                    'inverse' => ':attribute is niet voor :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Is datum',
                    'inverse' => 'Is niet datum',
                ],

                'summary' => [
                    'direct' => ':attribute is :date',
                    'inverse' => ':attribute is niet :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Is maand',
                    'inverse' => 'Is niet maand',
                ],

                'summary' => [
                    'direct' => ':attribute is :month',
                    'inverse' => ':attribute is niet :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Is jaar',
                    'inverse' => 'Is niet jaar',
                ],

                'summary' => [
                    'direct' => ':attribute is :year',
                    'inverse' => ':attribute is niet :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Seconden',
                'minute' => 'Minuten',
                'hour' => 'Uren',
                'day' => 'Dagen',
                'week' => 'Weken',
                'month' => 'Maanden',
                'quarter' => 'Kwartalen',
                'year' => 'Jaren',
            ],

            'presets' => [
                'past_decade' => 'Afgelopen decennium',
                'past_5_years' => 'Afgelopen 5 jaar',
                'past_2_years' => 'Afgelopen 2 jaar',
                'past_year' => 'Afgelopen jaar',
                'past_6_months' => 'Afgelopen 6 maanden',
                'past_quarter' => 'Afgelopen kwartaal',
                'past_month' => 'Afgelopen maand',
                'past_2_weeks' => 'Afgelopen 2 weken',
                'past_week' => 'Afgelopen week',
                'past_hour' => 'Afgelopen uur',
                'past_minute' => 'Afgelopen minuut',
                'this_decade' => 'Dit decennium',
                'this_year' => 'Dit jaar',
                'this_quarter' => 'Dit kwartaal',
                'this_month' => 'Deze maand',
                'today' => 'Vandaag',
                'this_hour' => 'Dit uur',
                'this_minute' => 'Deze minuut',
                'next_minute' => 'Komende minuut',
                'next_hour' => 'Komend uur',
                'next_week' => 'Volgende week',
                'next_2_weeks' => 'Volgende 2 weken',
                'next_month' => 'Volgende maand',
                'next_quarter' => 'Volgend kwartaal',
                'next_6_months' => 'Volgende 6 maanden',
                'next_year' => 'Volgend jaar',
                'next_2_years' => 'Volgende 2 jaar',
                'next_5_years' => 'Volgende 5 jaar',
                'next_decade' => 'Volgend decennium',
                'custom' => 'Aangepast',
            ],

            'form' => [

                'date' => [
                    'label' => 'Datum',
                ],

                'month' => [
                    'label' => 'Maand',
                ],

                'year' => [
                    'label' => 'Jaar',
                ],

                'mode' => [

                    'label' => 'Datumtype',

                    'options' => [
                        'absolute' => 'Specifieke datum',
                        'relative' => 'Relatieve periode',
                    ],

                ],

                'preset' => [
                    'label' => 'Tijdsperiode',
                ],

                'relative_value' => [
                    'label' => 'Aantal',
                ],

                'relative_unit' => [
                    'label' => 'Tijdseenheid',
                ],

                'tense' => [

                    'label' => 'Tijd',

                    'options' => [
                        'past' => 'Verleden',
                        'future' => 'Toekomst',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Is gelijk aan',
                    'inverse' => 'Is niet gelijk aan',
                ],

                'summary' => [
                    'direct' => ':attribute is gelijk aan :number',
                    'inverse' => ':attribute is niet gelijk aan :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Is maximaal',
                    'inverse' => 'Is groter dan',
                ],

                'summary' => [
                    'direct' => ':attribute is maximaal :number',
                    'inverse' => ':attribute is groter dan :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Is minimaal',
                    'inverse' => 'Is kleiner dan',
                ],

                'summary' => [
                    'direct' => ':attribute is minimaal :number',
                    'inverse' => ':attribute is kleiner dan :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Gemiddelde',
                    'summary' => 'Gemiddelde van :attribute',
                ],

                'max' => [
                    'label' => 'Maximum',
                    'summary' => 'Maximum van :attribute',
                ],

                'min' => [
                    'label' => 'Minimum',
                    'summary' => 'Minimum van :attribute',
                ],

                'sum' => [
                    'label' => 'Som',
                    'summary' => 'Som van :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Aggregaat',
                ],

                'number' => [
                    'label' => 'Getal',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Heeft',
                    'inverse' => 'Heeft niet',
                ],

                'summary' => [
                    'direct' => 'Heeft :count :relationship',
                    'inverse' => 'Heeft niet :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Heeft maximaal',
                    'inverse' => 'Heeft meer dan',
                ],

                'summary' => [
                    'direct' => 'Heeft maximaal :count :relationship',
                    'inverse' => 'Heeft meer dan :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Heeft minimaal',
                    'inverse' => 'Heeft minder dan',
                ],

                'summary' => [
                    'direct' => 'Heeft minimaal :count :relationship',
                    'inverse' => 'Heeft minder dan :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Is leeg',
                    'inverse' => 'Is niet leeg',
                ],

                'summary' => [
                    'direct' => ':relationship is leeg',
                    'inverse' => ':relationship is niet leeg',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Is',
                        'inverse' => 'Is niet',
                    ],

                    'multiple' => [
                        'direct' => 'Bevat',
                        'inverse' => 'Bevat niet',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship is :values',
                        'inverse' => ':relationship is niet :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship bevat :values',
                        'inverse' => ':relationship bevat niet :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' of ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Waarde',
                    ],

                    'values' => [
                        'label' => 'Waarden',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Aantal',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Is',
                    'inverse' => 'Is niet',
                ],

                'summary' => [
                    'direct' => ':attribute is :values',
                    'inverse' => ':attribute is niet :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' of ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Waarde',
                    ],

                    'values' => [
                        'label' => 'Waarden',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Bevat',
                    'inverse' => 'Bevat niet',
                ],

                'summary' => [
                    'direct' => ':attribute bevat :text',
                    'inverse' => ':attribute bevat niet :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Eindigt op',
                    'inverse' => 'Eindigt niet op',
                ],

                'summary' => [
                    'direct' => ':attribute eindigt op :text',
                    'inverse' => ':attribute eindigt niet op :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Is gelijk aan',
                    'inverse' => 'Is niet gelijk aan',
                ],

                'summary' => [
                    'direct' => ':attribute is gelijk aan :text',
                    'inverse' => ':attribute is niet gelijk aan :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Begint met',
                    'inverse' => 'Begint niet met',
                ],

                'summary' => [
                    'direct' => ':attribute begint met :text',
                    'inverse' => ':attribute begint niet met :text',
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
            'label' => 'Regel toevoegen',
        ],

        'add_rule_group' => [
            'label' => 'Regelgroep toevoegen',
        ],

    ],

];
