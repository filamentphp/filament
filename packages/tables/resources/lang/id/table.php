<?php

return [

    'column_manager' => [

        'heading' => 'Kolom',

        'position' => 'Posisi :position dari :count.',

        'position_in_group' => 'Posisi :position dari :count dalam :group.',

        'reordered' => ':column dipindahkan ke posisi :position dari :count.',

        'reordered_deferred' => ':column dipindahkan ke posisi :position dari :count. Terapkan kolom untuk menyimpan perubahan Anda.',

        'reordered_in_group' => ':column dipindahkan ke posisi :position dari :count dalam :group.',

        'reordered_in_group_deferred' => ':column dipindahkan ke posisi :position dari :count dalam :group. Terapkan kolom untuk menyimpan perubahan Anda.',

        'actions' => [

            'apply' => [
                'label' => 'Terapkan kolom',
            ],

            'move_up' => [
                'label' => 'Naikkan :column',
            ],

            'move_down' => [
                'label' => 'Turunkan :column',
            ],

            'reorder' => [
                'label' => 'Ubah urutan kolom',
            ],

            'reset' => [
                'label' => 'Atur ulang',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'Aksi|Aksi',
        ],

        'editable' => [
            'saving' => 'Menyimpan perubahan...',
            'saved' => 'Perubahan tersimpan.',
            'failure' => 'Penyimpanan tidak dapat dipastikan. Muat ulang halaman sebelum mencoba lagi.',
            'unavailable' => 'Item ini sudah tidak dapat diubah.',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'Ya',
                'false' => 'Tidak',
            ],

        ],

        'select' => [

            'loading_message' => 'Memuat...',

            'no_options_message' => 'Tidak ada opsi yang tersedia.',

            'no_search_results_message' => 'Tidak ada hasil yang sesuai dengan pencarian Anda.',

            'placeholder' => 'Pilih salah satu opsi',

            'searching_message' => 'Sedang mencari...',

            'search_prompt' => 'Ketik untuk mencari...',

        ],

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

        'reorder_record' => [
            'label' => 'Ubah urutan item :key',
        ],

        'move_record_up' => [
            'label' => 'Naikkan :label',
        ],

        'move_record_down' => [
            'label' => 'Turunkan :label',
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

        'column_manager' => [
            'label' => 'Pilih kolom',
        ],

        'toggle_record_content' => [
            'label' => 'Tampilkan/sembunyikan item :key',
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

            'relationship' => [
                'empty_option_label' => 'Tidak ada',
            ],

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

    'loading' => 'Memuat...',

    'reorder_indicator' => 'Seret dan lepaskan data ke dalam urutan.',

    'reordering' => [
        'position' => ':label, posisi :position dari :count.',
        'moved' => ':label dipindahkan ke posisi :position dari :count.',
    ],

    'result_count' => '{0} Tidak ada hasil|{1} :count hasil|[2,*] :count hasil',

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

    'default_model_label' => 'data',

];
