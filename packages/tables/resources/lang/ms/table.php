<?php

return [

    'column_manager' => [

        'heading' => 'Kolum',

    ],

    'columns' => [

        'actions' => [
            'label' => 'Tindakan|Tindakan',
        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'Tunjukkan kurang :count',
                'expand_list' => 'Tunjukkan :count lagi',
            ],

            'more_list_items' => 'dan :count lagi',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Pilih/nyahpilih semua item untuk tindakan pukal.',
        ],

        'bulk_select_record' => [
            'label' => 'Pilih/nyahpilih item :key untuk tindakan pukal.',
        ],

        'bulk_select_group' => [
            'label' => 'Pilih/nyahpilih kumpulan :title untuk tindakan pukal.',
        ],

        'search' => [
            'label' => 'Cari',
            'placeholder' => 'Carian',
            'indicator' => 'Carian',
        ],

    ],

    'summary' => [

        'heading' => 'Ringkasan',

        'subheadings' => [
            'all' => 'Semua :label',
            'group' => ':group ringkasan',
            'page' => 'Muka surat ini',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Purata',
            ],

            'count' => [
                'label' => 'Bilangan',
            ],

            'sum' => [
                'label' => 'Jumlah',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Selesai menyusun semula rekod',
        ],

        'enable_reordering' => [
            'label' => 'Menyusun semula rekod',
        ],

        'filter' => [
            'label' => 'Tapisan',
        ],

        'group' => [
            'label' => 'Kumpulan',
        ],

        'open_bulk_actions' => [
            'label' => 'Tindakan terbuka',
        ],

        'column_manager' => [
            'label' => 'Togol lajur',
        ],

    ],

    'empty' => [

        'heading' => 'Tiada rekod dijumpai',

        'description' => 'Cipta :model untuk bermula.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Gunakan tapisan',
            ],

            'remove' => [
                'label' => 'Buang tapisan',
            ],

            'remove_all' => [
                'label' => 'Buang semua tapisan',
                'tooltip' => 'Buang semua tapisan',
            ],

            'reset' => [
                'label' => 'Tetapkan semula tapisan',
            ],

        ],

        'heading' => 'Tapisan',

        'indicator' => 'Tapisan aktif',

        'multi_select' => [
            'placeholder' => 'Semua',
        ],

        'select' => [
            'placeholder' => 'Semua',

            'relationship' => [
                'empty_option_label' => 'Tiada',
            ],
        ],

        'trashed' => [

            'label' => 'Rekod telah dipadamkan',

            'only_trashed' => 'Hanya rekod yang dipadamkan',

            'with_trashed' => 'Dengan rekod yang dipadam',

            'without_trashed' => 'Tanpa rekod yang dipadam',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Kumpulan mengikut',
                'placeholder' => 'Kumpulan mengikut',
            ],

            'direction' => [

                'label' => 'Arah kumpulan',

                'options' => [
                    'asc' => 'Menaik',
                    'desc' => 'Menurun',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Seret dan lepaskan rekod mengikut susunan.',

    'selection_indicator' => [

        'selected_count' => '{1} 1 rekod dipilih|[2,*] :count rekod yang dipilih',

        'actions' => [

            'select_all' => [
                'label' => 'Pilih semua :count',
            ],

            'deselect_all' => [
                'label' => 'Nyahpilih semua',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Disusun mengikut',
            ],

            'direction' => [

                'label' => 'Arah susunan',

                'options' => [
                    'asc' => 'Menaik',
                    'desc' => 'Menurun',
                ],

            ],

        ],

    ],

    'default_model_label' => 'rekod',

];
