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
                        'label' => 'Alisin ang lahat ng napili',
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
            'title' => 'Tapos na ang pag-export',
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
            'title' => 'Masyadong malaki ang ie-export',
            'body' => 'Hindi ka maaaring mag-export ng higit sa 1 row nang sabay.|Hindi ka maaaring mag-export ng higit sa :count row nang sabay.',
        ],
        'no_columns' => [
            'title' => 'Walang napiling column',
            'body' => 'Pumili ng kahit isang column na ie-export.',
        ],
        'started' => [
            'title' => 'Nagsimula na ang pag-export',
            'body' => 'Nagsimula na ang pag-export mo at ipoproseso sa background ang 1 row. Makakatanggap ka ng notification na may download link kapag tapos na.|Nagsimula na ang pag-export mo at ipoproseso sa background ang :count row. Makakatanggap ka ng notification na may download link kapag tapos na.',
        ],
    ],
    'file_name' => 'export-:export_id-:model',
];
