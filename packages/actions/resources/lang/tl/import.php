<?php

return [

    'label' => 'I-import ang :label',

    'modal' => [

        'heading' => 'I-import ang :label',

        'form' => [

            'file' => [

                'placeholder' => 'Mag-upload ng CSV file',

                'rules' => [
                    'duplicate_columns' => '{0} Hindi dapat magkaroon ng higit sa isang walang-lamang column header ang file.|{1,*} Hindi dapat magkaroon ng duplicate na column header ang file: :columns.',
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

            'title' => 'Tapos na ang import',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'I-download ang impormasyon tungkol sa nabigong row|I-download ang impormasyon tungkol sa mga nabigong row',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Masyadong malaki ang na-upload na CSV file',
            'body' => 'Hindi puwedeng mag-import ng higit sa 1 row nang sabay.|Hindi puwedeng mag-import ng higit sa :count row nang sabay.',
        ],

        'started' => [
            'title' => 'Nagsimula na ang import',
            'body' => 'Nagsimula na ang import mo at 1 row ang ipoproseso sa background.|Nagsimula na ang import mo at :count row ang ipoproseso sa background.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'System error, makipag-ugnayan sa support.',
        'column_mapping_required_for_new_record' => 'Hindi na-map ang column na :attribute sa isang column sa file, pero kailangan ito sa paggawa ng mga bagong record.',
    ],

];
