<?php

return [

    'label' => 'Import :label',

    'modal' => [

        'heading' => 'Import :label',

        'form' => [

            'file' => [
                'label' => 'Fail',
                'placeholder' => 'Muat naik fail CSV',
            ],

            'columns' => [
                'label' => 'Lajur',
                'placeholder' => 'Pilih lajur',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Muat turun contoh fail CSV',
            ],

            'import' => [
                'label' => 'Import',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import selesai',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Muat turun maklumat tentang baris yang gagal|Muat turun maklumat tentang baris yang gagal',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Fail CSV yang dimuat naik terlalu besar',
            'body' => 'Anda tidak boleh mengimport lebih daripada 1 baris sekaligus.|Anda tidak boleh mengimport lebih daripada :count baris sekaligus.',
        ],

        'started' => [
            'title' => 'Import dimulakan',
            'body' => 'Import anda telah bermula dan 1 baris akan diproses di latar belakang.|Import anda telah bermula dan :count baris akan diproses di latar belakang.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'ralat',
        'system_error' => 'Ralat sistem, sila hubungi sokongan.',
    ],

];
