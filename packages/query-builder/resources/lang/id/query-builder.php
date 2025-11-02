<?php

return [

    'label' => 'Penyusun Kueri',

    'form' => [

        'operator' => [
            'label' => 'Operator',
        ],

        'or_groups' => [

            'label' => 'Grup',

            'block' => [
                'label' => 'Disjungsi (ATAU)',
                'or' => 'ATAU',
            ],

        ],

        'rules' => [

            'label' => 'Aturan',

            'item' => [
                'and' => 'DAN',
            ],

        ],

    ],

    'no_rules' => '(Tanpa aturan)',

    'item_separators' => [
        'and' => 'DAN',
        'or' => 'ATAU',
    ],

    'operators' => [

        'is_filled' => [

            'label' => [
                'direct' => 'Terisi',
                'inverse' => 'Kosong',
            ],

            'summary' => [
                'direct' => ':attribute terisi',
                'inverse' => ':attribute kosong',
            ],

        ],

        'boolean' => [

            'is_true' => [

                'label' => [
                    'direct' => 'Ya',
                    'inverse' => 'Tidak',
                ],

                'summary' => [
                    'direct' => ':attribute adalah ya',
                    'inverse' => ':attribute adalah tidak',
                ],

            ],

        ],

        'date' => [

            'is_after' => [

                'label' => [
                    'direct' => 'Setelah',
                    'inverse' => 'Tidak setelah',
                ],

                'summary' => [
                    'direct' => ':attribute setelah :date',
                    'inverse' => ':attribute tidak setelah :date',
                ],

            ],

            'is_before' => [

                'label' => [
                    'direct' => 'Sebelum',
                    'inverse' => 'Tidak sebelum',
                ],

                'summary' => [
                    'direct' => ':attribute sebelum :date',
                    'inverse' => ':attribute tidak sebelum :date',
                ],

            ],

            'is_date' => [

                'label' => [
                    'direct' => 'Pada tanggal',
                    'inverse' => 'Tidak pada tanggal',
                ],

                'summary' => [
                    'direct' => ':attribute pada tanggal :date',
                    'inverse' => ':attribute tidak pada tanggal :date',
                ],

            ],

            'is_month' => [

                'label' => [
                    'direct' => 'Pada bulan',
                    'inverse' => 'Tidak pada bulan',
                ],

                'summary' => [
                    'direct' => ':attribute pada bulan :month',
                    'inverse' => ':attribute tidak pada bulan :month',
                ],

            ],

            'is_year' => [

                'label' => [
                    'direct' => 'Pada tahun',
                    'inverse' => 'Tidak pada tahun',
                ],

                'summary' => [
                    'direct' => ':attribute pada tahun :year',
                    'inverse' => ':attribute tidak pada tahun :year',
                ],

            ],

            'form' => [

                'date' => [
                    'label' => 'Tanggal',
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
                    'direct' => 'Sama dengan',
                    'inverse' => 'Tidak sama dengan',
                ],

                'summary' => [
                    'direct' => ':attribute sama dengan :number',
                    'inverse' => ':attribute tidak sama dengan :number',
                ],

            ],

            'is_max' => [

                'label' => [
                    'direct' => 'Maksimum',
                    'inverse' => 'Lebih dari',
                ],

                'summary' => [
                    'direct' => 'Maksimum :attribute adalah :number',
                    'inverse' => ':attribute lebih dari :number',
                ],

            ],

            'is_min' => [

                'label' => [
                    'direct' => 'Minimum',
                    'inverse' => 'Kurang dari',
                ],

                'summary' => [
                    'direct' => 'Minimum :attribute adalah :number',
                    'inverse' => ':attribute kurang dari :number',
                ],

            ],

            'aggregates' => [

                'average' => [
                    'label' => 'Rata-rata',
                    'summary' => 'Rata-rata :attribute',
                ],

                'max' => [
                    'label' => 'Tertinggi',
                    'summary' => ':attribute tertinggi',
                ],

                'min' => [
                    'label' => 'Terendah',
                    'summary' => ':attribute terendah',
                ],

                'sum' => [
                    'label' => 'Total',
                    'summary' => 'Total :attribute',
                ],

            ],

            'form' => [

                'aggregate' => [
                    'label' => 'Agregat',
                ],

                'number' => [
                    'label' => 'Angka',
                ],

            ],

        ],

        'relationship' => [

            'equals' => [

                'label' => [
                    'direct' => 'Memiliki',
                    'inverse' => 'Tidak memiliki',
                ],

                'summary' => [
                    'direct' => 'Memiliki :count :relationship',
                    'inverse' => 'Tidak memiliki :count :relationship',
                ],

            ],

            'has_max' => [

                'label' => [
                    'direct' => 'Memiliki hingga',
                    'inverse' => 'Memiliki lebih dari',
                ],

                'summary' => [
                    'direct' => 'Memiliki hingga :count :relationship',
                    'inverse' => 'Memiliki lebih dari :count :relationship',
                ],

            ],

            'has_min' => [

                'label' => [
                    'direct' => 'Memiliki setidaknya',
                    'inverse' => 'Memiliki kurang dari',
                ],

                'summary' => [
                    'direct' => 'Memiliki setidaknya :count :relationship',
                    'inverse' => 'Memiliki kurang dari :count :relationship',
                ],

            ],

            'is_empty' => [

                'label' => [
                    'direct' => 'Tidak memiliki',
                    'inverse' => 'Memiliki',
                ],

                'summary' => [
                    'direct' => 'tidak memiliki :relationship',
                    'inverse' => 'memiliki :relationship',
                ],

            ],

            'is_related_to' => [

                'label' => [

                    'single' => [
                        'direct' => 'Adalah',
                        'inverse' => 'Bukan',
                    ],

                    'multiple' => [
                        'direct' => 'Terdapat',
                        'inverse' => 'Tidak terdapat',
                    ],

                ],

                'summary' => [

                    'single' => [
                        'direct' => ':relationship adalah :values',
                        'inverse' => ':relationship bukanlah :values',
                    ],

                    'multiple' => [
                        'direct' => ':relationship terdapat :values',
                        'inverse' => ':relationship tidak terdapat :values',
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
                    'label' => 'Jumlah',
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
                        'final' => ' or ',
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
                    'direct' => 'Terdapat',
                    'inverse' => 'Tidak terdapat',
                ],

                'summary' => [
                    'direct' => ':attribute terdapat :text',
                    'inverse' => ':attribute tidak terdapat :text',
                ],

            ],

            'ends_with' => [

                'label' => [
                    'direct' => 'Diakhiri dengan',
                    'inverse' => 'Tidak diakhiri dengan',
                ],

                'summary' => [
                    'direct' => ':attribute diakhiri dengan :text',
                    'inverse' => ':attribute tidak diakhiri dengan :text',
                ],

            ],

            'equals' => [

                'label' => [
                    'direct' => 'Sama dengan',
                    'inverse' => 'Tidak sama dengan',
                ],

                'summary' => [
                    'direct' => ':attribute sama dengan :text',
                    'inverse' => ':attribute tidak sama dengan :text',
                ],

            ],

            'starts_with' => [

                'label' => [
                    'direct' => 'Dimulai dengan',
                    'inverse' => 'Tidak dimulai dengan',
                ],

                'summary' => [
                    'direct' => ':attribute dimulai dengan :text',
                    'inverse' => ':attribute tidak dimulai dengan :text',
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
            'label' => 'Tambah aturan',
        ],

        'add_rule_group' => [
            'label' => 'Tambah grup aturan',
        ],

    ],

];
