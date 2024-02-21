<?php

return [

    'label' => 'Eksport :label',

    'modal' => [

        'heading' => 'Eksport :label',

        'form' => [

            'columns' => [

                'label' => 'Kolum',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column diaktifkan',
                    ],

                    'label' => [
                        'label' => ':column label',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Eksport',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Eksport selesai',

            'actions' => [

                'download_csv' => [
                    'label' => 'Muat turun .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Muat turun .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Eksport terlalu besar',
            'body' => 'Anda tidak boleh mengeluarkan lebih dari 1 baris pada satu masa.|Anda tidak boleh mengeluarkan lebih dari :count baris pada satu masa.',
        ],

        'started' => [
            'title' => 'Eksport bermula',
            'body' => 'Eksport anda telah bermula dan 1 baris akan diproses di belakang tabir.|Eksport anda telah bermula dan :count baris akan diproses di belakang tabir.',
        ],

    ],

    'file_name' => 'eksport-:export_id-:model',

];
