<?php

return [

    'label' => 'Importar :label',

    'modal' => [

        'heading' => 'Importar :label',

        'form' => [

            'file' => [
                'label' => 'Ficheiro',
                'placeholder' => 'Carregar um ficheiro CSV',
            ],

            'columns' => [
                'label' => 'Colunas',
                'placeholder' => 'Seleccione uma coluna',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Descarregar um ficheiro CSV de exemplo',
            ],

            'import' => [
                'label' => 'Importar',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Importação completa',

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
