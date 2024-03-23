<?php

return [

    'label' => 'Importera :label',

    'modal' => [

        'heading' => 'Importera :label',

        'form' => [

            'file' => [
                'label' => 'Fil',
                'placeholder' => 'Ladda upp en CSV-fil',
            ],

            'columns' => [
                'label' => 'Kolumner',
                'placeholder' => 'Välj en kolumn',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Ladda ner exempelfil (CSV)',
            ],

            'import' => [
                'label' => 'Importera',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import slutförd',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Ladda ner information om den misslyckade raden|Ladda ner information om de misslyckade raderna',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Den uppladdade CSV-filen är för stor',
            'body' => 'Du kan inte importera fler än 1 rad åt gången.|Du kan inte importera fler än :count rader åt gången.',
        ],

        'started' => [
            'title' => 'Importen startades',
            'body' => 'Din import har börjat och 1 rad kommer att bearbetas i bakgrunden.|Din import har börjat och :count rader kommer att bearbetas i bakgrunden.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-misslyckade-rader',
        'error_header' => 'fel',
        'system_error' => 'Systemfel, vänligen kontakta support.',
    ],

];
