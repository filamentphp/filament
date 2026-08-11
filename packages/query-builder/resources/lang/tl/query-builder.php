<?php

return [

    'label' => 'Query builder',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Mga group',

            'group' => [
                'label' => 'Group',
            ],

            'block' => [
                'label' => 'Kondisyong OR',
                'or' => 'OR',
            ],

        ],

        'rules' => [

            'label' => 'Rules',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(Walang rule)',

    'max_rules_reached_tooltip' => 'Naabot mo na ang maximum na :count rule.',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'May laman',
                'inverse' => 'Blangko',
            ],

            'summary' => [
                'direct' => 'May laman ang :attribute',
                'inverse' => 'Blangko ang :attribute',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Oo',
                    'inverse' => 'Hindi',
                ],

                'summary' => [
                    'direct' => 'Oo ang :attribute',
                    'inverse' => 'Hindi ang :attribute',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Pagkatapos ng',
                    'inverse' => 'Hindi pagkatapos ng',
                ],

                'summary' => [
                    'direct' => 'Pagkatapos ng :date ang :attribute',
                    'inverse' => 'Hindi pagkatapos ng :date ang :attribute',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Bago ang',
                    'inverse' => 'Hindi bago ang',
                ],

                'summary' => [
                    'direct' => 'Bago ang :date ang :attribute',
                    'inverse' => 'Hindi bago ang :date ang :attribute',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Petsa ay',
                    'inverse' => 'Petsa ay hindi',
                ],

                'summary' => [
                    'direct' => ':date ang :attribute',
                    'inverse' => 'Hindi :date ang :attribute',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Buwan ay',
                    'inverse' => 'Buwan ay hindi',
                ],

                'summary' => [
                    'direct' => ':month ang :attribute',
                    'inverse' => 'Hindi :month ang :attribute',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Taon ay',
                    'inverse' => 'Taon ay hindi',
                ],

                'summary' => [
                    'direct' => ':year ang :attribute',
                    'inverse' => 'Hindi :year ang :attribute',
                ],

            ],

            'unit_labels' => [
                'second' => 'Segundo',
                'minute' => 'Minuto',
                'hour' => 'Oras',
                'day' => 'Araw',
                'week' => 'Linggo',
                'month' => 'Buwan',
                'quarter' => 'Quarter',
                'year' => 'Taon',
            ],

            'presets' => [
                'past_decade' => 'Nakaraang dekada',
                'past_5_years' => 'Nakaraang 5 taon',
                'past_2_years' => 'Nakaraang 2 taon',
                'past_year' => 'Nakaraang taon',
                'past_6_months' => 'Nakaraang 6 na buwan',
                'past_quarter' => 'Nakaraang quarter',
                'past_month' => 'Nakaraang buwan',
                'past_2_weeks' => 'Nakaraang 2 linggo',
                'past_week' => 'Nakaraang linggo',
                'past_hour' => 'Nakaraang oras',
                'past_minute' => 'Nakaraang minuto',
                'this_decade' => 'Dekadang ito',
                'this_year' => 'Taong ito',
                'this_quarter' => 'Quarter na ito',
                'this_month' => 'Buwang ito',
                'today' => 'Ngayong araw',
                'this_hour' => 'Oras na ito',
                'this_minute' => 'Minutong ito',
                'next_minute' => 'Susunod na minuto',
                'next_hour' => 'Susunod na oras',
                'next_week' => 'Susunod na linggo',
                'next_2_weeks' => 'Susunod na 2 linggo',
                'next_month' => 'Susunod na buwan',
                'next_quarter' => 'Susunod na quarter',
                'next_6_months' => 'Susunod na 6 na buwan',
                'next_year' => 'Susunod na taon',
                'next_2_years' => 'Susunod na 2 taon',
                'next_5_years' => 'Susunod na 5 taon',
                'next_decade' => 'Susunod na dekada',
                'custom' => 'Custom',
            ],

            'form' => [

                'date' => [
                    'label' => 'Petsa',
                ],

                'month' => [
                    'label' => 'Buwan',
                ],

                'year' => [
                    'label' => 'Taon',
                ],

                'mode' => [

                    'label' => 'Uri ng petsa',

                    'options' => [
                        'absolute' => 'Partikular na petsa',
                        'relative' => 'Rolling window',
                    ],

                ],

                'preset' => [
                    'label' => 'Panahon',
                ],

                'relative_value' => [
                    'label' => 'Ilan',
                ],

                'relative_unit' => [
                    'label' => 'Yunit ng oras',
                ],

                'tense' => [

                    'label' => 'Panahunan',

                    'options' => [
                        'past' => 'Nakaraan',
                        'future' => 'Hinaharap',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Katumbas ng',
                    'inverse' => 'Hindi katumbas ng',
                ],

                'summary' => [
                    'direct' => 'Katumbas ng :number ang :attribute',
                    'inverse' => 'Hindi katumbas ng :number ang :attribute',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Maximum ay',
                    'inverse' => 'Mas malaki sa',
                ],

                'summary' => [
                    'direct' => 'Maximum na :number ang :attribute',
                    'inverse' => 'Mas malaki sa :number ang :attribute',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Minimum ay',
                    'inverse' => 'Mas mababa sa',
                ],

                'summary' => [
                    'direct' => 'Minimum na :number ang :attribute',
                    'inverse' => 'Mas mababa sa :number ang :attribute',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Average',
                    'summary' => 'Average ng :attribute',
                ],

                'max' => [
                    'label' => 'Max',
                    'summary' => 'Max ng :attribute',
                ],

                'min' => [
                    'label' => 'Min',
                    'summary' => 'Min ng :attribute',
                ],

                'sum' => [
                    'label' => 'Kabuuan',
                    'summary' => 'Kabuuan ng :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Aggregate',
                ],

                'number' => [
                    'label' => 'Numero',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'May',
                    'inverse' => 'Walang',
                ],

                'summary' => [
                    'direct' => 'May :count :relationship',
                    'inverse' => 'Walang :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'May maximum',
                    'inverse' => 'May higit sa',
                ],

                'summary' => [
                    'direct' => 'May maximum na :count :relationship',
                    'inverse' => 'May higit sa :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'May minimum',
                    'inverse' => 'May mas kaunti sa',
                ],

                'summary' => [
                    'direct' => 'May minimum na :count :relationship',
                    'inverse' => 'May mas kaunti sa :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Walang laman',
                    'inverse' => 'May laman',
                ],

                'summary' => [
                    'direct' => 'Walang laman ang :relationship',
                    'inverse' => 'May laman ang :relationship',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Ay',
                        'inverse' => 'Ay hindi',
                    ],

                    'multiple' => [
                        'direct' => 'Naglalaman ng',
                        'inverse' => 'Hindi naglalaman ng',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':values ang :relationship',
                        'inverse' => 'Hindi :values ang :relationship',
                    ],

                    'multiple' => [
                        'direct' => 'Naglalaman ng :values ang :relationship',
                        'inverse' => 'Hindi naglalaman ng :values ang :relationship',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' o ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Value',
                    ],

                    'values' => [
                        'label' => 'Values',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Bilang',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Ay',
                    'inverse' => 'Ay hindi',
                ],

                'summary' => [
                    'direct' => ':values ang :attribute',
                    'inverse' => 'Hindi :values ang :attribute',
                    'values_glue' => [
                        ', ',
                        'final' => ' o ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Value',
                    ],

                    'values' => [
                        'label' => 'Values',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Naglalaman ng',
                    'inverse' => 'Hindi naglalaman ng',
                ],

                'summary' => [
                    'direct' => 'Naglalaman ng :text ang :attribute',
                    'inverse' => 'Hindi naglalaman ng :text ang :attribute',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Nagtatapos sa',
                    'inverse' => 'Hindi nagtatapos sa',
                ],

                'summary' => [
                    'direct' => 'Nagtatapos sa :text ang :attribute',
                    'inverse' => 'Hindi nagtatapos sa :text ang :attribute',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Katumbas ng',
                    'inverse' => 'Hindi katumbas ng',
                ],

                'summary' => [
                    'direct' => 'Katumbas ng :text ang :attribute',
                    'inverse' => 'Hindi katumbas ng :text ang :attribute',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Nagsisimula sa',
                    'inverse' => 'Hindi nagsisimula sa',
                ],

                'summary' => [
                    'direct' => 'Nagsisimula sa :text ang :attribute',
                    'inverse' => 'Hindi nagsisimula sa :text ang :attribute',
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
            'label' => 'Magdagdag ng rule',
        ],

        'add_rule_group' => [
            'label' => 'Magdagdag ng OR',
        ],

    ],

];
