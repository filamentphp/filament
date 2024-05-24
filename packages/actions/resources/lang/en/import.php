<?php

return [

    'label' => 'Import :label',

    'modal' => [

        'heading' => 'Import :label',

        'form' => [

            'file' => [

                'label' => 'File',

                'placeholder' => 'Upload a CSV file',

                'rules' => [
                    'duplicate_columns' => '{0} The file must not contain more than one empty column header.|{1,*} The file must not contain duplicate column headers: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Columns',
                'placeholder' => 'Select a column',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Download example CSV file',
            ],

            'import' => [
                'label' => 'Import',
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
            'title' => 'Uploaded CSV file is too large',
            'body' => 'You may not import more than 1 row at once.|You may not import more than :count rows at once.',
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
