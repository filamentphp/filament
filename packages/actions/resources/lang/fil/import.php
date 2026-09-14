<?php

return [
    'label' => 'I-import ang :label',
    'modal' => [
        'heading' => 'I-import ang :label',
        'form' => [
            'file' => [
                'label' => 'File',
                'placeholder' => 'Mag-upload ng CSV file',
                'rules' => [
                    'duplicate_columns' => '{0} Hindi dapat magkaroon ng higit sa isang blankong column header ang file.|{1,*} Hindi dapat magkaroon ng mga duplicate na column header ang file: :columns.',
                ],
            ],
            'columns' => [
                'label' => 'Mga column',
                'placeholder' => 'Pumili ng column',
            ],
        ],
        'actions' => [
            'download_example' => [
                'label' => 'I-download ang halimbawang CSV file',
            ],
            'import' => [
                'label' => 'I-import',
            ],
        ],
    ],
    'notifications' => [
        'completed' => [
            'title' => 'Tapos na ang pag-import',
            'actions' => [
                'download_failed_rows_csv' => [
                    'label' => 'I-download ang impormasyon tungkol sa hindi na-import na row|I-download ang impormasyon tungkol sa mga hindi na-import na row',
                ],
            ],
        ],
        'max_rows' => [
            'title' => 'Masyadong malaki ang na-upload na CSV file',
            'body' => 'Hindi ka maaaring mag-import ng higit sa 1 row nang sabay.|Hindi ka maaaring mag-import ng higit sa :count row nang sabay.',
        ],
        'started' => [
            'title' => 'Nagsimula na ang pag-import',
            'body' => 'Nagsimula na ang pag-import mo at ipoproseso sa background ang 1 row.|Nagsimula na ang pag-import mo at ipoproseso sa background ang :count row.',
        ],
    ],
    'example_csv' => [
        'file_name' => ':importer-halimbawa',
    ],
    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-mga-hindi-na-import-na-row',
        'error_header' => 'error',
        'system_error' => 'May error sa system. Makipag-ugnayan sa support.',
        'column_mapping_required_for_new_record' => 'Hindi itinugma ang :attribute column sa anumang column sa file, pero kailangan ito para gumawa ng mga bagong rekord.',
    ],
];
