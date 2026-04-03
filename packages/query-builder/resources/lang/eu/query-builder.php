<?php

return [

    'label' => 'Kontsulta-eraikitzailea',

    'form' => [

        'operator' => [
            'label' => 'Eragilea',
        ],

        'or_groups' => [

            'label' => 'Taldeak',

            'block' => [
                'label' => 'EDO baldintza',
                'or' => 'EDO',
            ],

        ],

        'rules' => [

            'label' => 'Arauak',

            'item' => [
                'and' => 'ETA',
            ],

        ],

    ],

    'no_rules' => '(Araurik ez)',

    'item_separators' => [
        'and' => 'ETA',
        'or' => 'EDO',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Beteta dago',
                'inverse' => 'Hutsik dago',
            ],

            'summary' => [
                'direct' => ':attribute beteta dago',
                'inverse' => ':attribute hutsik dago',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Egia da',
                    'inverse' => 'Gezurra da',
                ],

                'summary' => [
                    'direct' => ':attribute egia da',
                    'inverse' => ':attribute gezurra da',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Ondorengoa da',
                    'inverse' => 'Ez da ondorengoa',
                ],

                'summary' => [
                    'direct' => ':attribute :date ondorengoa da',
                    'inverse' => ':attribute ez da :date ondorengoa',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Aurrekoa da',
                    'inverse' => 'Ez da aurrekoa',
                ],

                'summary' => [
                    'direct' => ':attribute :date aurrekoa da',
                    'inverse' => ':attribute ez da :date aurrekoa',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Data da',
                    'inverse' => 'Ez da data',
                ],

                'summary' => [
                    'direct' => ':attribute :date da',
                    'inverse' => ':attribute ez da :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Hilabetea da',
                    'inverse' => 'Ez da hilabetea',
                ],

                'summary' => [
                    'direct' => ':attribute :month da',
                    'inverse' => ':attribute ez da :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Urtea da',
                    'inverse' => 'Ez da urtea',
                ],

                'summary' => [
                    'direct' => ':attribute :year da',
                    'inverse' => ':attribute ez da :year',
                ],

            ],

            'unit_labels' => [
                'second' => 'Segundoak',
                'minute' => 'Minutuak',
                'hour' => 'Orduak',
                'day' => 'Egunak',
                'week' => 'Asteak',
                'month' => 'Hilabeteak',
                'quarter' => 'Hiruhilekoak',
                'year' => 'Urteak',
            ],

            'presets' => [
                'past_decade' => 'Azken hamarkada',
                'past_5_years' => 'Azken 5 urteak',
                'past_2_years' => 'Azken 2 urteak',
                'past_year' => 'Azken urtea',
                'past_6_months' => 'Azken 6 hilabeteak',
                'past_quarter' => 'Azken hiruhilekoa',
                'past_month' => 'Azken hilabetea',
                'past_2_weeks' => 'Azken 2 asteak',
                'past_week' => 'Azken astea',
                'past_hour' => 'Azken ordua',
                'past_minute' => 'Azken minutua',
                'this_decade' => 'Hamarkada hau',
                'this_year' => 'Urte hau',
                'this_quarter' => 'Hiruhileko hau',
                'this_month' => 'Hilabete hau',
                'today' => 'Gaur',
                'this_hour' => 'Ordu hau',
                'this_minute' => 'Minutu hau',
                'next_minute' => 'Hurrengo minutua',
                'next_hour' => 'Hurrengo ordua',
                'next_week' => 'Hurrengo astea',
                'next_2_weeks' => 'Hurrengo 2 asteak',
                'next_month' => 'Hurrengo hilabetea',
                'next_quarter' => 'Hurrengo hiruhilekoa',
                'next_6_months' => 'Hurrengo 6 hilabeteak',
                'next_year' => 'Hurrengo urtea',
                'next_2_years' => 'Hurrengo 2 urteak',
                'next_5_years' => 'Hurrengo 5 urteak',
                'next_decade' => 'Hurrengo hamarkada',
                'custom' => 'Pertsonalizatua',
            ],

            'form' => [

                'date' => [
                    'label' => 'Data',
                ],

                'month' => [
                    'label' => 'Hilabetea',
                ],

                'year' => [
                    'label' => 'Urtea',
                ],

                'mode' => [

                    'label' => 'Data mota',

                    'options' => [
                        'absolute' => 'Data zehatza',
                        'relative' => 'Denbora-tartea',
                    ],

                ],

                'preset' => [
                    'label' => 'Denbora-tartea',
                ],

                'relative_value' => [
                    'label' => 'Zenbat',
                ],

                'relative_unit' => [
                    'label' => 'Denbora-unitatea',
                ],

                'tense' => [

                    'label' => 'Denbora',

                    'options' => [
                        'past' => 'Iragana',
                        'future' => 'Etorkizuna',
                    ],

                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Berdina da',
                    'inverse' => 'Ez da berdina',
                ],

                'summary' => [
                    'direct' => ':attribute :number berdina da',
                    'inverse' => ':attribute ez da :number berdina',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Gehienez da',
                    'inverse' => 'Handiagoa da',
                ],

                'summary' => [
                    'direct' => ':attribute gehienez :number da',
                    'inverse' => ':attribute :number baino handiagoa da',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Gutxienez da',
                    'inverse' => 'Txikiagoa da',
                ],

                'summary' => [
                    'direct' => ':attribute gutxienez :number da',
                    'inverse' => ':attribute :number baino txikiagoa da',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Batezbestekoa',
                    'summary' => ':attribute-ren batezbestekoa',
                ],

                'max' => [
                    'label' => 'Gehienez',
                    'summary' => ':attribute-ren gehienekoa',
                ],

                'min' => [
                    'label' => 'Gutxienez',
                    'summary' => ':attribute-ren gutxienekoa',
                ],

                'sum' => [
                    'label' => 'Batura',
                    'summary' => ':attribute-ren batura',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregazioa',
                ],

                'number' => [
                    'label' => 'Zenbakia',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Dauka',
                    'inverse' => 'Ez dauka',
                ],

                'summary' => [
                    'direct' => ':count :relationship dauka',
                    'inverse' => 'Ez dauka :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Gehienez dauka',
                    'inverse' => 'Gehiago dauka',
                ],

                'summary' => [
                    'direct' => 'Gehienez :count :relationship dauka',
                    'inverse' => ':count :relationship baino gehiago dauka',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Gutxienez dauka',
                    'inverse' => 'Gutxiago dauka',
                ],

                'summary' => [
                    'direct' => 'Gutxienez :count :relationship dauka',
                    'inverse' => ':count :relationship baino gutxiago dauka',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Hutsik dago',
                    'inverse' => 'Ez dago hutsik',
                ],

                'summary' => [
                    'direct' => ':relationship hutsik dago',
                    'inverse' => ':relationship ez dago hutsik',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Da',
                        'inverse' => 'Ez da',
                    ],

                    'multiple' => [
                        'direct' => 'Dauka',
                        'inverse' => 'Ez dauka',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship :values da',
                        'inverse' => ':relationship ez da :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship :values dauka',
                        'inverse' => ':relationship ez dauka :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' edo ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Balioa',
                    ],

                    'values' => [
                        'label' => 'Balioak',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Zenbatekoa',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Da',
                    'inverse' => 'Ez da',
                ],

                'summary' => [
                    'direct' => ':attribute :values da',
                    'inverse' => ':attribute ez da :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' edo ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Balioa',
                    ],

                    'values' => [
                        'label' => 'Balioak',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Dauka',
                    'inverse' => 'Ez dauka',
                ],

                'summary' => [
                    'direct' => ':attribute :text dauka',
                    'inverse' => ':attribute ez dauka :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Honekin amaitzen da',
                    'inverse' => 'Ez da honekin amaitzen',
                ],

                'summary' => [
                    'direct' => ':attribute :text-ekin amaitzen da',
                    'inverse' => ':attribute ez da :text-ekin amaitzen',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Berdina da',
                    'inverse' => 'Ez da berdina',
                ],

                'summary' => [
                    'direct' => ':attribute :text berdina da',
                    'inverse' => ':attribute ez da :text berdina',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Honekin hasten da',
                    'inverse' => 'Ez da honekin hasten',
                ],

                'summary' => [
                    'direct' => ':attribute :text-ekin hasten da',
                    'inverse' => ':attribute ez da :text-ekin hasten',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Testua',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Araua gehitu',
        ],

        'add_rule_group' => [
            'label' => 'EDO gehitu',
        ],

    ],

];
