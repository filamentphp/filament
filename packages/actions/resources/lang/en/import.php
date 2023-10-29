<?php

return [

    'label' => 'Import :label',

    'modal' => [

        'heading' => 'Import :label',

        'form' => [

            'file' => [
                'label' => 'File',
                'placeholder' => 'Upload a .csv file',
            ],

            'columns' => [
                'label' => 'Columns',
                'placeholder' => 'Select a column',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Download an example .csv file.',
            ],

            'import' => [
                'label' => 'Start import',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import completed',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Download information about the failed row|Download information about the failed rows',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'That file is too large to import',
            'body' => 'You may not import more than :count row at once.',
        ],

        'started' => [
            'title' => 'Import started',
            'body' => 'Your import has begun and 1 row will be processed in the background.|Your import has begun and :count rows will be processed in the background.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'System error, please contact support.',
    ],

];
