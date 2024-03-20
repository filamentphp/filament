<?php

return [

    'label' => 'Impor :label',

    'modal' => [

        'heading' => 'Impor :label',

        'form' => [

            'file' => [
                'label' => 'Berkas',
                'placeholder' => 'Unggah berkas CSV',
            ],

            'columns' => [
                'label' => 'Kolom',
                'placeholder' => 'Pilih kolom',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Unduh contoh berkas CSV',
            ],

            'import' => [
                'label' => 'Impor',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Impor selesai',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Unduh informasi baris yang gagal diimpor',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Ukuran berkas CSV terlalu besar',
            'body' => 'Anda tidak dapat mengimpor lebih dari :count baris sekaligus.',
        ],

        'started' => [
            'title' => 'Impor dimulai',
            'body' => 'Mulai mengimpor :count baris dan proses akan berjalan di belakang layar.',
        ],

    ],

    'example_csv' => [
        'file_name' => 'contoh-:importer',
    ],

    'failure_csv' => [
        'file_name' => 'impor-:import_id-:csv_name-gagal',
        'error_header' => 'kesalahan',
        'system_error' => 'Terjadi kesalahan sistem, harap hubungi tim support.',
    ],

];
