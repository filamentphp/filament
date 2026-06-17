<?php

return [

    'label' => 'Ekspor :label',

    'modal' => [

        'heading' => 'Ekspor :label',

        'form' => [

            'columns' => [

                'label' => 'Kolom',

                'actions' => [

                    'select_all' => [
                        'label' => 'Pilih semua',
                    ],

                    'deselect_all' => [
                        'label' => 'Batal pilih semua',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => ':column diaktifkan',
                    ],

                    'label' => [
                        'label' => 'Label :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Ekspor',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Ekspor selesai',

            'actions' => [

                'download_csv' => [
                    'label' => 'Unduh .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Unduh .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Ekspor terlalu besar',
            'body' => 'Anda tidak dapat mengekspor lebih dari :count sekaligus.',
        ],

        'no_columns' => [
            'title' => 'Tidak ada kolom yang dipilih',
            'body' => 'Pilih setidaknya satu kolom untuk diekspor.',
        ],

        'started' => [
            'title' => 'Ekspor dimulai',
            'body' => 'Mulai mengekspor :count baris dan proses akan berjalan di belakang layar.',
        ],

    ],

    'file_name' => 'ekspor-:export_id-:model',

];
