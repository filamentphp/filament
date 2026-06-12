<?php

return [

    'label' => 'Užklausos kūrimas',

    'form' => [

        'operator' => [
            'label' => 'Palyginimas',
        ],

        'or_groups' => [

            'label' => 'Grupės',

            'block' => [
                'label' => 'Blokas (ARBA)',
                'or' => 'ARBA',
            ],

        ],

        'rules' => [

            'label' => 'Taisyklės',

            'item' => [
                'and' => 'IR',
            ],

        ],

    ],

    'no_rules' => '(Nėra taisyklių)',

    'item_separators' => [
        'and' => 'IR',
        'or' => 'ARBA',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Užpildyta',
                'inverse' => 'Neužpildyta',
            ],

            'summary' => [
                'direct' => ':attribute užpildyta',
                'inverse' => ':attribute neužpildyta',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Yra teigiamas',
                    'inverse' => 'Nėra teigiamas',
                ],

                'summary' => [
                    'direct' => ':attribute yra teigiamas',
                    'inverse' => ':attribute nėra teigiamas',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Yra po',
                    'inverse' => 'Nėra po',
                ],

                'summary' => [
                    'direct' => ':attribute yra po :date',
                    'inverse' => ':attribute nėra po :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Yra prieš',
                    'inverse' => 'Nėra prieš',
                ],

                'summary' => [
                    'direct' => ':attribute yra prieš :date',
                    'inverse' => ':attribute nėra prieš :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Yra data',
                    'inverse' => 'Nėra data',
                ],

                'summary' => [
                    'direct' => ':attribute yra :date',
                    'inverse' => ':attribute nėra :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Yra mėnesis',
                    'inverse' => 'Nėra mėnesis',
                ],

                'summary' => [
                    'direct' => ':attribute yra :month',
                    'inverse' => ':attribute nėra :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Yra metai',
                    'inverse' => 'Nėra metai',
                ],

                'summary' => [
                    'direct' => ':attribute yra :year',
                    'inverse' => ':attribute nėra :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Sekundės',
                'minute' => 'Minutės',
                'hour' => 'Valandos',
                'day' => 'Dienos',
                'week' => 'Savaitės',
                'month' => 'Mėnesiai',
                'quarter' => 'Ketvirčiai',
                'year' => 'Metai',
            ],

            'presets' => [
                'past_decade' => 'Praėjęs dešimtmetis',
                'past_5_years' => 'Praėję 5 metai',
                'past_2_years' => 'Praėję 2 metai',
                'past_year' => 'Praėję metai',
                'past_6_months' => 'Praėję 6 mėnesiai',
                'past_quarter' => 'Praėję ketvirtis',
                'past_month' => 'Praėjęs mėnesis',
                'past_2_weeks' => 'Praėjusios 2 savaitės',
                'past_week' => 'Praėjusi savaitė',
                'past_hour' => 'Praėjusi valanda',
                'past_minute' => 'Praėjusi minutė',
                'this_decade' => 'Šis dešimtmetis',
                'this_year' => 'Šie metai',
                'this_quarter' => 'Šis ketvirtis',
                'this_month' => 'Šis mėnesis',
                'today' => 'Šiandien',
                'this_hour' => 'Ši valanda',
                'this_minute' => 'Ši minutė',
                'next_minute' => 'Kita minutė',
                'next_hour' => 'Kita valanda',
                'next_week' => 'Kita savaitė',
                'next_2_weeks' => 'Kitos 2 savaitės',
                'next_month' => 'Kitas mėnesis',
                'next_quarter' => 'Kitas ketvirtis',
                'next_6_months' => 'Kiti 6 mėnesiai',
                'next_year' => 'Kiti metai',
                'next_2_years' => 'Kiti 2 metai',
                'next_5_years' => 'Kiti 5 metai',
                'next_decade' => 'Kitas dešimtmetis',
                'custom' => 'Pasirinktinis',
            ],

            'form' => [

                'date' => [
                    'label' => 'Data',
                ],

                'month' => [
                    'label' => 'Mėnesis',
                ],

                'year' => [
                    'label' => 'Metai',
                ],

                'mode' => [

                    'label' => 'Datos tipas',

                    'options' => [
                        'absolute' => 'Specifinė data',
                        'relative' => 'Slankusis langas',
                    ],

                ],

                'preset' => [
                    'label' => 'Laiko periodas',
                ],

                'relative_value' => [
                    'label' => 'Kiek',
                ],

                'relative_unit' => [
                    'label' => 'Laiko vienetas',
                ],

                'tense' => [

                    'label' => 'Laiko nuosaka',
                    'options' => [
                        'past' => 'Praeitis',
                        'future' => 'Ateitis',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Lygu',
                    'inverse' => 'Nelygu',
                ],

                'summary' => [
                    'direct' => ':attribute lygu :number',
                    'inverse' => ':attribute nelygu :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Yra didžiausias',
                    'inverse' => 'Yra didesnis už',
                ],

                'summary' => [
                    'direct' => ':attribute yra didžiausias :number',
                    'inverse' => ':attribute yra didesnis už :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Yra mažiausias',
                    'inverse' => 'Yra mažesnis už',
                ],

                'summary' => [
                    'direct' => ':attribute yra mažiausias :number',
                    'inverse' => ':attribute yra mažesnis už :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Vidurkis',
                    'summary' => ':attribute vidurkis',
                ],

                'max' => [
                    'label' => 'Maksimumas',
                    'summary' => 'Didžiausias :attribute',
                ],

                'min' => [
                    'label' => 'Minimumas',
                    'summary' => 'Mažiausias :attribute',
                ],

                'sum' => [
                    'label' => 'Suma',
                    'summary' => ':attribute suma',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Suminis rodiklis',
                ],

                'number' => [
                    'label' => 'Skaičius',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Turi',
                    'inverse' => 'Neturi',
                ],

                'summary' => [
                    'direct' => 'Turi :count :relationship',
                    'inverse' => 'Neturi :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Turi daugiausiai',
                    'inverse' => 'Turi daugiau nei',
                ],

                'summary' => [
                    'direct' => 'Turi daugiausiai :count :relationship',
                    'inverse' => 'Turi daugiau nei :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Turi mažiausiai',
                    'inverse' => 'Turi mažiau nei',
                ],

                'summary' => [
                    'direct' => 'Turi mažiausiai :count :relationship',
                    'inverse' => 'Turi mažiau nei :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Yra tuščias',
                    'inverse' => 'Nėra tuščias',
                ],

                'summary' => [
                    'direct' => ':relationship yra tuščias',
                    'inverse' => ':relationship nėra tuščias',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Yra',
                        'inverse' => 'Nėra',
                    ],

                    'multiple' => [
                        'direct' => 'Yra vienas iš',
                        'inverse' => 'Nėra nė vienas iš',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship yra :values',
                        'inverse' => ':relationship nėra :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship turi :values',
                        'inverse' => ':relationship neturi :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' arba ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Reikšmė',
                    ],

                    'values' => [
                        'label' => 'Reikšmės',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Kiekis',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Yra',
                    'inverse' => 'Nėra',
                ],

                'summary' => [
                    'direct' => ':attribute yra :values',
                    'inverse' => ':attribute nėra :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' arba ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Reikšmė',
                    ],

                    'values' => [
                        'label' => 'Reikšmės',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Turi',
                    'inverse' => 'Neturi',
                ],

                'summary' => [
                    'direct' => ':attribute turi :text',
                    'inverse' => ':attribute neturi :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Turi pabaigą',
                    'inverse' => 'Neturi pabaigos',
                ],

                'summary' => [
                    'direct' => ':attribute baigiasi :text',
                    'inverse' => ':attribute nesibaigia :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Yra',
                    'inverse' => 'Nėra',
                ],

                'summary' => [
                    'direct' => ':attribute lygus :text',
                    'inverse' => ':attribute nelygus :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Turi pradžią',
                    'inverse' => 'Neturi pradžios',
                ],

                'summary' => [
                    'direct' => ':attribute prasideda :text',
                    'inverse' => ':attribute neprasideda :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Tekstas',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Pridėti taisyklę',
        ],

        'add_rule_group' => [
            'label' => 'Pridėti taisyklių grupę',
        ],

    ],

];
