<?php

return [

    'column_toggle' => [

        'heading' => 'Kolum',

    ],

    'columns' => [

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
            'label' => 'Penapis',
        ],

        'group' => [
            'label' => 'Kumpulan',
        ],

        'open_bulk_actions' => [
            'label' => 'Tindakan terbuka',
        ],

        'toggle_columns' => [
            'label' => 'Togol lajur',
        ],

    ],

    'empty' => [

        'heading' => 'Tiada rekod dijumpai',

        'description' => 'Cipta :model untuk bermula.',

    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Buang penapis',
            ],

            'remove_all' => [
                'label' => 'Buang semua penapis',
                'tooltip' => 'Buang semua penapis',
            ],

            'reset' => [
                'label' => 'Tetapkan semula penapis',
            ],

        ],

        'heading' => 'Penapis',

        'indicator' => 'Penapis aktif',

        'multi_select' => [
            'placeholder' => 'Semua',
        ],

        'select' => [
            'placeholder' => 'Semua',
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

];
