<?php

return [

    'label' => 'Importa :label',

    'modal' => [

        'heading' => 'Importa :label',

        'form' => [

            'file' => [
                'label' => 'File',
                'placeholder' => 'Carica un file CSV',
            ],

            'columns' => [
                'label' => 'Colonne',
                'placeholder' => 'Seleziona una colonna',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Scarica un file CSV di esempio',
            ],

            'import' => [
                'label' => 'Importa',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Importazione completata',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Scarica le informazioni riguardo le righe fallite|Scarica l\'informazione riguardo la riga fallita',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Il file CSV caricato è troppo grande',
            'body' => 'Non puoi importare più di 1 riga alla volta.|Non puoi importare più di :count  righe alla volta.',
        ],

        'started' => [
            'title' => 'L\'importazione è iniziata',
            'body' => 'L\'importazione è iniziata e 1 riga verrà elaborata in background.|L\'importazione è iniziata e :count righe verranno elaborate in background.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'errore',
        'system_error' => 'Errore di sistema, per favore contatta il supporto.',
    ],

];
