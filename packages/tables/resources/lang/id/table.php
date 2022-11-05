<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'dan :count lainnya',
        ],

        'messages' => [
            'copied' => 'Disalin',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Cari',
            'placeholder' => 'Cari',
        ],

    ],

    'pagination' => [

        'label' => 'Navigasi halaman',

        'overview' => 'Menampilkan :first sampai :last dari :total hasil',

        'fields' => [

            'records_per_page' => [

                'label' => 'per halaman',

                'options' => [
                    'all' => 'Semua',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Ke halaman :page',
            ],

            'next' => [
                'label' => 'Selanjutnya',
            ],

            'previous' => [
                'label' => 'Sebelumnya',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Selesaikan pengurutan ulang data',
        ],

        'enable_reordering' => [
            'label' => 'Urutkan ulang data',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'open_actions' => [
            'label' => 'Tindakan',
        ],

        'toggle_columns' => [
            'label' => 'Pilih kolom',
        ],

    ],

    'empty' => [
        'heading' => 'Tidak ada data yang ditemukan',
    ],

    'filters' => [

        'buttons' => [

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

    'reorder_indicator' => 'Seret dan lepaskan data ke dalam urutan.',

    'selection_indicator' => [

        'selected_count' => ':count data dipilih.',

        'buttons' => [

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
