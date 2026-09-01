<?php

return [

    'label' => 'Pembina pertanyaan',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Kumpulan',

            'block' => [
                'label' => 'Disjunction (ATAU)',
                'or' => 'ATAU',
            ],

        ],

        'rules' => [

            'label' => 'Peraturan',

            'item' => [
                'and' => 'DAN',
            ],

        ],

    ],

    'no_rules' => '(Tiada peraturan)',

    'item_separators' => [
        'and' => 'DAN',
        'or' => 'ATAU',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Dipenuhi',
                'inverse' => 'Kosong',
            ],

            'summary' => [
                'direct' => ':attribute dipenuhi',
                'inverse' => ':attribute kosong',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Betul',
                    'inverse' => 'Salah',
                ],

                'summary' => [
                    'direct' => ':attribute adalah betul',
                    'inverse' => ':attribute adalah salah',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Adalah selepas',
                    'inverse' => 'Adalah tidak selepas',
                ],

                'summary' => [
                    'direct' => ':attribute adalah selepas :date',
                    'inverse' => ':attribute adalah tidak selepas :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Adalah sebelum',
                    'inverse' => 'Adalah tidak sebelum',
                ],

                'summary' => [
                    'direct' => ':attribute adalah sebelum :date',
                    'inverse' => ':attribute adalah tidak sebelum :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Adalah tarikh',
                    'inverse' => 'Adalah bukan tarikh',
                ],

                'summary' => [
                    'direct' => ':attribute adalah :date',
                    'inverse' => ':attribute bukan :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Adalah bulan',
                    'inverse' => 'Adalah bukan bulan',
                ],

                'summary' => [
                    'direct' => ':attribute adalah :month',
                    'inverse' => ':attribute bukan :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Adalah tahun',
                    'inverse' => 'Adalah bukan tahun',
                ],

                'summary' => [
                    'direct' => ':attribute adalah :year',
                    'inverse' => ':attribute bukan :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Tarikh',
                ],

                'month' => [
                    'label' => 'Bulan',
                ],

                'year' => [
                    'label' => 'Tahun',
                ],

            ],

        ],

        'number' => [

            'equals' => [

                'label' => [
                    'direct' => 'Sama',
                    'inverse' => 'Tidak sama',
                ],

                'summary' => [
                    'direct' => ':attribute sama :number',
                    'inverse' => ':attribute tidak sama :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Adalah maksimum',
                    'inverse' => 'Adalah lebih besar daripada',
                ],

                'summary' => [
                    'direct' => ':attribute adalah maksimum :number',
                    'inverse' => ':attribute adalah lebih besar daripada :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Adalah minimum',
                    'inverse' => 'Adalah kurang daripada',
                ],

                'summary' => [
                    'direct' => ':attribute adalah minimum :number',
                    'inverse' => ':attribute adalah kurang daripada :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Purata',
                    'summary' => 'Purata :attribute',
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
                    'label' => 'Jumlah',
                    'summary' => 'Jumlah :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregat',
                ],

                'number' => [
                    'label' => 'Nombor',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Mempunyai',
                    'inverse' => 'Tidak mempunyai',
                ],

                'summary' => [
                    'direct' => 'Mempunyai :count :relationship',
                    'inverse' => 'Tidak mempunyai :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Mempunyai maksimum',
                    'inverse' => 'Mempunyai lebih daripada',
                ],

                'summary' => [
                    'direct' => 'Mempunyai maksimum :count :relationship',
                    'inverse' => 'Mempunyai lebih daripada :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Mempunyai minimum',
                    'inverse' => 'Mempunyai kurang daripada',
                ],

                'summary' => [
                    'direct' => 'Mempunyai minimum :count :relationship',
                    'inverse' => 'Mempunyai kurang daripada :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Kosong',
                    'inverse' => 'Tidak kosong',
                ],

                'summary' => [
                    'direct' => ':relationship kosong',
                    'inverse' => ':relationship tidak kosong',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Adalah',
                        'inverse' => 'Adalah tidak',
                    ],

                    'multiple' => [
                        'direct' => 'Mengandungi',
                        'inverse' => 'Tidak mengandungi',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship adalah :values',
                        'inverse' => ':relationship bukan :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship mengandungi :values',
                        'inverse' => ':relationship tidak mengandungi :values',
                    ],

                    'values_glue' => [
                        0 => ', ',
                        'final' => ' atau ',
                    ],

                ],

                'form' => [

                    'value' => [
                        'label' => 'Nilai',
                    ],

                    'values' => [
                        'label' => 'Nilai',
                    ],

                ],

            ],

            'form' => [

                'count' => [
                    'label' => 'Kira',
                ],

            ],

        ],

        'select' => [

            'is' => [

                'label' => [
                    'direct' => 'Adalah',
                    'inverse' => 'Bukan',
                ],

                'summary' => [
                    'direct' => ':attribute adalah :values',
                    'inverse' => ':attribute bukan :values',
                    'values_glue' => [
                        ', ',
                        'final' => ' atau ',
                    ],
                ],

                'form' => [

                    'value' => [
                        'label' => 'Nilai',
                    ],

                    'values' => [
                        'label' => 'Nilai',
                    ],

                ],

            ],

        ],

        'text' => [

            'contains' => [

                'label' => [
                    'direct' => 'Mengandungi',
                    'inverse' => 'Tidak mengandungi',
                ],

                'summary' => [
                    'direct' => ':attribute mengandungi :text',
                    'inverse' => ':attribute tidak mengandungi :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Berakhir dengan',
                    'inverse' => 'Tidak berakhir dengan',
                ],

                'summary' => [
                    'direct' => ':attribute berakhir dengan :text',
                    'inverse' => ':attribute tidak berakhir dengan :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Sama',
                    'inverse' => 'Tidak sama',
                ],

                'summary' => [
                    'direct' => ':attribute sama :text',
                    'inverse' => ':attribute tidak sama :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Bermula dengan',
                    'inverse' => 'Tidak bermula dengan',
                ],

                'summary' => [
                    'direct' => ':attribute bermula dengan :text',
                    'inverse' => ':attribute tidak bermula dengan :text',
                ],

            ],

            'form' => [

                'text' => [
                    'label' => 'Teks',
                ],

            ],

        ],

    ],

    'actions' => [

        'add_rule' => [
            'label' => 'Tambah peraturan',
        ],

        'add_rule_group' => [
            'label' => 'Tambahkan kumpulan peraturan',
        ],

    ],

];
