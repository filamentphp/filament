<?php

return [

    'label' => 'I-export ang :label',

    'modal' => [

        'heading' => 'I-export ang :label',

        'form' => [

            'columns' => [

                'label' => 'Mga column',

                'actions' => [

                    'select_all' => [
                        'label' => 'Piliin lahat',
                    ],

                    'deselect_all' => [
                        'label' => 'Alisin ang lahat ng pili',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => 'Naka-enable ang :column',
                    ],

                    'label' => [
                        'label' => 'Label ng :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'I-export',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Tapos na ang export',

            'actions' => [

                'download_csv' => [
                    'label' => 'I-download ang .csv',
                ],

                'download_xlsx' => [
                    'label' => 'I-download ang .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Masyadong malaki ang export',
            'body' => 'Hindi puwedeng mag-export ng higit sa 1 row nang sabay.|Hindi puwedeng mag-export ng higit sa :count row nang sabay.',
        ],

        'no_columns' => [
            'title' => 'Walang napiling column',
            'body' => 'Pumili ng kahit isang column na ie-export.',
        ],

        'started' => [
            'title' => 'Nagsimula na ang export',
            'body' => 'Nagsimula na ang export mo at 1 row ang ipoproseso sa background. Makakatanggap ka ng notification na may download link kapag tapos na ito.|Nagsimula na ang export mo at :count row ang ipoproseso sa background. Makakatanggap ka ng notification na may download link kapag tapos na ito.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
