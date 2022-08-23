<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'dan :count lainnya',
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
            'label' => 'Selesaikan mengurutkan ulang data',
        ],

        'enable_reordering' => [
            'label' => 'Mengurutkan ulang data',
        ],

        'filter' => [
            'label' => 'Filter',
        ],

        'open_actions' => [
            'label' => 'Aksi',
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

            'reset' => [
                'label' => 'Atur ulang filter',
                'tooltip' => 'Atur ulang filter',
            ],

            'close' => [
                'label' => 'Tutup',
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

            'only_trashed' => 'Hanya data yang sudah dihapus',

            'with_trashed' => 'Dengan data yang sudah dihapus',

            'without_trashed' => 'Tanpa data yang sudah dihapus',

        ],

    ],

    'reorder_indicator' => 'Seret dan lepaskan data kedalam urutan.',

    'selection_indicator' => [

        'selected_count' => ':count dipilih.',

        'buttons' => [

            'select_all' => [
                'label' => 'Pilih semua (:count)',
            ],

            'deselect_all' => [
                'label' => 'Batalkan semua pilihan',
            ],

        ],

    ],

];
