<?php

return [
    'label' => 'Tagabuo ng query',
    'form' => [
        'operator' => [
            'label' => 'Operador',
        ],
        'or_groups' => [
            'label' => 'Mga Grupo',
            'group' => [
                'label' => 'Grupo',
            ],
            'block' => [
                'label' => 'Kondisyong OR',
                'or' => 'OR',
            ],
        ],
        'rules' => [
            'label' => 'Mga Rule',
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
                    'direct' => 'Tama',
                    'inverse' => 'Mali',
                ],
                'summary' => [
                    'direct' => 'Tama ang :attribute',
                    'inverse' => 'Mali ang :attribute',
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
                    'direct' => 'Ang :attribute ay pagkatapos ng :date',
                    'inverse' => 'Ang :attribute ay hindi pagkatapos ng :date',
                ],
            ],
            'is_before' => [
                'label' => [
                    'direct' => 'Bago ang',
                    'inverse' => 'Hindi bago ang',
                ],
                'summary' => [
                    'direct' => 'Ang :attribute ay bago ang :date',
                    'inverse' => 'Ang :attribute ay hindi bago ang :date',
                ],
            ],
            'is_date' => [
                'label' => [
                    'direct' => 'Ang petsa ay',
                    'inverse' => 'Ang petsa ay hindi',
                ],
                'summary' => [
                    'direct' => 'Ang :attribute ay :date',
                    'inverse' => 'Ang :attribute ay hindi :date',
                ],
            ],
            'is_month' => [
                'label' => [
                    'direct' => 'Ang buwan ay',
                    'inverse' => 'Ang buwan ay hindi',
                ],
                'summary' => [
                    'direct' => 'Ang :attribute ay :month',
                    'inverse' => 'Ang :attribute ay hindi :month',
                ],
            ],
            'is_year' => [
                'label' => [
                    'direct' => 'Ang taon ay',
                    'inverse' => 'Ang taon ay hindi',
                ],
                'summary' => [
                    'direct' => 'Ang :attribute ay :year',
                    'inverse' => 'Ang :attribute ay hindi :year',
                ],
            ],
            'unit_labels' => [
                'second' => 'Mga Segundo',
                'minute' => 'Mga Minuto',
                'hour' => 'Mga Oras',
                'day' => 'Mga Araw',
                'week' => 'Mga Linggo',
                'month' => 'Mga Buwan',
                'quarter' => 'Mga Quarter',
                'year' => 'Mga Taon',
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
                'today' => 'Ngayon',
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
                'custom' => 'Custom na panahon',
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
                        'relative' => 'Gumagalaw na saklaw',
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
                    'label' => 'Panahon ng pandiwa',
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
                    'direct' => 'Ang :attribute ay katumbas ng :number',
                    'inverse' => 'Ang :attribute ay hindi katumbas ng :number',
                ],
            ],
            'is_max' => [
                'label' => [
                    'direct' => 'Maximum na',
                    'inverse' => 'Higit sa',
                ],
                'summary' => [
                    'direct' => 'Ang maximum na :attribute ay :number',
                    'inverse' => 'Ang :attribute ay higit sa :number',
                ],
            ],
            'is_min' => [
                'label' => [
                    'direct' => 'Minimum na',
                    'inverse' => 'Mas mababa sa',
                ],
                'summary' => [
                    'direct' => 'Ang minimum na :attribute ay :number',
                    'inverse' => 'Ang :attribute ay mas mababa sa :number',
                ],
            ],
            'aggregates' => [
                'average' => [
                    'label' => 'Katamtaman',
                    'summary' => 'Average ng :attribute',
                ],
                'max' => [
                    'label' => 'Pinakamataas',
                    'summary' => 'Pinakamataas na :attribute',
                ],
                'min' => [
                    'label' => 'Pinakamababa',
                    'summary' => 'Pinakamababang :attribute',
                ],
                'sum' => [
                    'label' => 'Kabuuan',
                    'summary' => 'Kabuuan ng :attribute',
                ],
            ],
            'form' => [
                'aggregate' => [
                    'label' => 'Pinagsama-samang halaga',
                ],
                'number' => [
                    'label' => 'Numero',
                ],
            ],
        ],
        'relationship' => [
            'equals' => [
                'label' => [
                    'direct' => 'Mayroon',
                    'inverse' => 'Wala',
                ],
                'summary' => [
                    'direct' => 'May :count :relationship',
                    'inverse' => 'Walang :count :relationship',
                ],
            ],
            'has_max' => [
                'label' => [
                    'direct' => 'May maximum na',
                    'inverse' => 'May higit sa',
                ],
                'summary' => [
                    'direct' => 'May maximum na :count :relationship',
                    'inverse' => 'May higit sa :count :relationship',
                ],
            ],
            'has_min' => [
                'label' => [
                    'direct' => 'May minimum na',
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
                        'direct' => 'Ang :relationship ay :values',
                        'inverse' => 'Ang :relationship ay hindi :values',
                    ],
                    'multiple' => [
                        'direct' => 'Naglalaman ang :relationship ng :values',
                        'inverse' => 'Hindi naglalaman ang :relationship ng :values',
                    ],
                    'values_glue' => [
                        0 => ', ',
                        'final' => ' o ',
                    ],
                ],
                'form' => [
                    'value' => [
                        'label' => 'Halaga',
                    ],
                    'values' => [
                        'label' => 'Mga Halaga',
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
                    'direct' => 'Ang :attribute ay :values',
                    'inverse' => 'Ang :attribute ay hindi :values',
                    'values_glue' => [
                        0 => ', ',
                        'final' => ' o ',
                    ],
                ],
                'form' => [
                    'value' => [
                        'label' => 'Halaga',
                    ],
                    'values' => [
                        'label' => 'Mga Halaga',
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
                    'direct' => 'Naglalaman ang :attribute ng :text',
                    'inverse' => 'Hindi naglalaman ang :attribute ng :text',
                ],
            ],
            'ends_with' => [
                'label' => [
                    'direct' => 'Nagtatapos sa',
                    'inverse' => 'Hindi nagtatapos sa',
                ],
                'summary' => [
                    'direct' => 'Nagtatapos ang :attribute sa :text',
                    'inverse' => 'Hindi nagtatapos ang :attribute sa :text',
                ],
            ],
            'equals' => [
                'label' => [
                    'direct' => 'Katumbas ng',
                    'inverse' => 'Hindi katumbas ng',
                ],
                'summary' => [
                    'direct' => 'Ang :attribute ay katumbas ng :text',
                    'inverse' => 'Ang :attribute ay hindi katumbas ng :text',
                ],
            ],
            'starts_with' => [
                'label' => [
                    'direct' => 'Nagsisimula sa',
                    'inverse' => 'Hindi nagsisimula sa',
                ],
                'summary' => [
                    'direct' => 'Nagsisimula ang :attribute sa :text',
                    'inverse' => 'Hindi nagsisimula ang :attribute sa :text',
                ],
            ],
            'form' => [
                'text' => [
                    'label' => 'Teksto',
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
