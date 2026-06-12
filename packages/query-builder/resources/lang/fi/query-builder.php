<?php

return [

    'label' => 'Kyselyn rakentaja',

    'form' => [

        'operator' => [
            'label' => 'Operaattori',
        ],

        'or_groups' => [

            'label' => 'Ryhmät',

            'block' => [
                'label' => 'Disjunktio (OR)',
                'or' => 'OR',
            ],

        ],

        'rules' => [

            'label' => 'Säännöt',

            'item' => [
                'and' => 'AND',
            ],

        ],

    ],

    'no_rules' => '(Ei sääntöjä)',

    'item_separators' => [
        'and' => 'AND',
        'or' => 'OR',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'On täytetty',
                'inverse' => 'On tyhjä',
            ],

            'summary' => [
                'direct' => ':attribute on täytetty',
                'inverse' => ':attribute on tyhjä',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'On tosi',
                    'inverse' => 'On epätosi',
                ],

                'summary' => [
                    'direct' => ':attribute on tosi',
                    'inverse' => ':attribute on epätosi',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'On jälkeen',
                    'inverse' => 'Ei ole jälkeen',
                ],

                'summary' => [
                    'direct' => ':attribute on :date jälkeen',
                    'inverse' => ':attribute ei ole :date jälkeen',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'On ennen',
                    'inverse' => 'Ei ole ennen',
                ],

                'summary' => [
                    'direct' => ':attribute on ennen :date',
                    'inverse' => ':attribute ei ole ennen :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'On päiväys',
                    'inverse' => 'Ei ole päiväys',
                ],

                'summary' => [
                    'direct' => ':attribute on :date',
                    'inverse' => ':attribute ei ole :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'On kuukausi',
                    'inverse' => 'Ei ole kuukausi',
                ],

                'summary' => [
                    'direct' => ':attribute on :month',
                    'inverse' => ':attribute ei ole :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'On vuosi',
                    'inverse' => 'Ei ole vuosi',
                ],

                'summary' => [
                    'direct' => ':attribute on :year',
                    'inverse' => ':attribute ei ole :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Sekunttia',
                'minute' => 'Minuuttia',
                'hour' => 'Tuntia',
                'day' => 'Päivää',
                'week' => 'Viikkoa',
                'month' => 'Kuukautta',
                'quarter' => 'Neljännestä',
                'year' => 'Vuotta',
            ],

            'presets' => [
                'past_decade' => 'Edellinen vuosikymmen',
                'past_5_years' => 'Edelliset 5 vuotta',
                'past_2_years' => 'Edelliset 2 vuotta',
                'past_year' => 'Edellinen vuosi',
                'past_6_months' => 'Edelliset 6 kuukautta',
                'past_quarter' => 'Edellinen neljännes',
                'past_month' => 'Edellinen kuukausi',
                'past_2_weeks' => 'Edelliset 2 viikkoa',
                'past_week' => 'Edellinen viikko',
                'past_hour' => 'Edellinen tunti',
                'past_minute' => 'Edellinen minuutti',
                'this_decade' => 'Tämä vuosikymmen',
                'this_year' => 'Tämä vuosi',
                'this_quarter' => 'Tämä neljännes',
                'this_month' => 'Tämä kuukausi',
                'today' => 'Tänään',
                'this_hour' => 'Tämä tunti',
                'this_minute' => 'Tämä minuutti',
                'next_minute' => 'Seuraava minuutti',
                'next_hour' => 'Seuraava tunti',
                'next_week' => 'Seuraava viikko',
                'next_2_weeks' => 'Seuraavat 2 viikkoa',
                'next_month' => 'Seuraava kuukausi',
                'next_quarter' => 'Seuraava neljännes',
                'next_6_months' => 'Seuraavat 6 kuukautta',
                'next_year' => 'Seuraava vuosi',
                'next_2_years' => 'Seuraavat 2 vuotta',
                'next_5_years' => 'Seuraavat 5 vuotta',
                'next_decade' => 'Seuraava vuosikymmen',
                'custom' => 'Mukautettu',
            ],

            'form' => [

                'date' => [
                    'label' => 'Päivä',
                ],

                'month' => [
                    'label' => 'Kuukausi',
                ],

                'year' => [
                    'label' => 'Vuosi',
                ],

                'mode' => [

                    'label' => 'Päivämäärän tyyppi',

                    'options' => [
                        'absolute' => 'Tietty päivä',
                        'relative' => 'Liikkuva aikaväli',
                    ],

                ],

                'preset' => [
                    'label' => 'Aikaväli',
                ],

                'relative_value' => [
                    'label' => 'Kuinka monta',
                ],

                'relative_unit' => [
                    'label' => 'Yksikkö',
                ],

                'tense' => [

                    'label' => 'Aikamuoto',

                    'options' => [
                        'past' => 'Menneisyys',
                        'future' => 'Tulevaisuus',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'On yhtä kuin',
                    'inverse' => 'Ei ole yhtä kuin',
                ],

                'summary' => [
                    'direct' => ':attribute on yhtä kuin :number',
                    'inverse' => ':attribute ei ole yhtä kuin :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'On enimmillään',
                    'inverse' => 'On suurempi kuin',
                ],

                'summary' => [
                    'direct' => ':attribute on enimmillään :number',
                    'inverse' => ':attribute on suurempi kuin :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'On vähimmillään',
                    'inverse' => 'On vähemmän kuin',
                ],

                'summary' => [
                    'direct' => ':attribute on vähimmillään :number',
                    'inverse' => ':attribute on vähemmän kuin :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Keskiarvo',
                    'summary' => 'Keskiarvo :attribute',
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
                    'label' => 'Summa',
                    'summary' => 'Summa :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Kokonaisuus',
                ],

                'number' => [
                    'label' => 'Numero',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Omistaa',
                    'inverse' => 'Ei omista',
                ],

                'summary' => [
                    'direct' => 'Omistaa :count :relationship',
                    'inverse' => 'Ei omista :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'On enimmillään',
                    'inverse' => 'On enemmän kuin',
                ],

                'summary' => [
                    'direct' => 'On enimmillään :count :relationship',
                    'inverse' => 'On enemmän kuin :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'On vähintään',
                    'inverse' => 'On vähemmän kuin',
                ],

                'summary' => [
                    'direct' => 'On vähintään :count :relationship',
                    'inverse' => 'On vähemmän kuin :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'On tyhjä',
                    'inverse' => 'Ei ole tyhjä',
                ],

                'summary' => [
                    'direct' => ':relationship on tyhjä',
                    'inverse' => ':relationship ei ole tyhjä',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'On',
                        'inverse' => 'Ei ole',
                    ],

                    'multiple' => [
                        'direct' => 'Sisältää',
                        'inverse' => 'Ei sisällä',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship on :values',
                        'inverse' => ':relationship ei ole :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship sisältää :values',
                        'inverse' => ':relationship ei sisällä :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' tai ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Arvo',
                    ],

                    'values' => [
                        'label' => 'Arvot',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Määrä',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'On',
                    'inverse' => 'Ei ole',
                ],

                'summary' => [
                    'direct' => ':attribute on :values',
                    'inverse' => ':attribute ei ole :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' tai ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Arvo',
                    ],

                    'values' => [
                        'label' => 'Arvot',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Sisältää',
                    'inverse' => 'Ei sisällä',
                ],

                'summary' => [
                    'direct' => ':attribute sisältää :text',
                    'inverse' => ':attribute ei sisällä :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Loppuu',
                    'inverse' => 'Ei lopu',
                ],

                'summary' => [
                    'direct' => ':attribute loppuu :text',
                    'inverse' => ':attribute ei lopu :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'On yhtä kuin',
                    'inverse' => 'Ei ole yhtä kuin',
                ],

                'summary' => [
                    'direct' => ':attribute equals :text',
                    'inverse' => ':attribute does not equal :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Alkaa',
                    'inverse' => 'Ei ala',
                ],

                'summary' => [
                    'direct' => ':attribute alkaa :text',
                    'inverse' => ':attribute ei ala :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Teksti',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Lisää sääntö',
        ],

        'add_rule_group' => [
            'label' => 'Lisää sääntöryhmä',
        ],

    ],

];
