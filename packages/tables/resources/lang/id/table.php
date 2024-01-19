<?php

return [

    'column_toggle' => [

        'heading' => 'Kolom',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Sembunyikan :count lainnya',
                'expand_list' => 'Tampilkan :count lainnya',
            ],

            'more_list_items' => 'dan :count lainnya',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Buat/batalkan pilihan semua item untuk tindakan massal.',
        ],

        'bulk_select_record' => [
            'label' => 'Buat/batalkan pilihan item :key untuk tindakan massal.',
        ],

        'bulk_select_group' => [
            'label' => 'Buat/batalkan pilihan grup :title untuk tindakan massal.',
        ],

        'search' => [
            'label' => 'Cari',
            'placeholder' => 'Cari',
            'indicator' => 'Pencarian',
        ],

    ],

    'summary' => [

        'heading' => 'Rangkuman',

        'subheadings' => [
            'all' => 'Semua :label',
            'group' => 'Rangkuman :group',
            'page' => 'Halaman ini',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Rata-rata',
            ],

            'count' => [
                'label' => 'Jumlah',
            ],

            'sum' => [
                'label' => 'Total',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Selesaikan pengurutan ulang data',
        ],

        'enable_reordering' => [
            'label' => 'Urutkan ulang data',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'group' => [
            'label' => 'Grup',
        ],

        'open_bulk_actions' => [
            'label' => 'Tindakan',
        ],

        'toggle_columns' => [
            'label' => 'Pilih kolom',
        ],

    ],

    'empty' => [

        'heading' => 'Tidak ada data yang ditemukan',

        'description' => 'Buat :model untuk memulai.',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'Terapkan filter',
            ],

            'remove' => [
                'label' => 'Hapus filter',
            ],

            'remove_all' => [
                'label' => 'Hapus semua filter',
                'tooltip' => 'Hapus semua filter',
            ],

            'reset' => [
                'label' => 'Atur ulang filter',
            ],

        ],

        'heading' => 'Filter',

        'indicator' => 'Filter aktif',

        'multi_select' => [
            'placeholder' => 'Semua',
        ],

        'select' => [
            'placeholder' => 'Semua',
        ],

        'trashed' => [

            'label' => 'Data yang dihapus',

            'only_trashed' => 'Hanya data yang dihapus',

            'with_trashed' => 'Dengan data yang dihapus',

            'without_trashed' => 'Tanpa data yang dihapus',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Kelompokkan berdasar',
                'placeholder' => 'Kelompokkan berdasar',
            ],

            'direction' => [

                'label' => 'Urutan grup',

                'options' => [
                    'asc' => 'Naik',
                    'desc' => 'Turun',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Seret dan lepaskan data ke dalam urutan.',

    'selection_indicator' => [

        'selected_count' => ':count data dipilih',

        'actions' => [

            'select_all' => [
                'label' => 'Pilih semua (:count)',
            ],

            'deselect_all' => [
                'label' => 'Batalkan semua pilihan',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Urutkan menurut',
            ],

            'direction' => [

                'label' => 'Arah urutan',

                'options' => [
                    'asc' => 'Naik',
                    'desc' => 'Turun',
                ],

            ],

        ],

    ],

];
