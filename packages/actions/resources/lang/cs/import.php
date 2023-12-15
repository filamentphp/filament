<?php

return [

    'label' => 'Import :label',

    'modal' => [

        'heading' => 'Import :label',

        'form' => [

            'file' => [
                'label' => 'Soubor',
                'placeholder' => 'Nahrát CSV soubor',
            ],

            'columns' => [
                'label' => 'Sloupce',
                'placeholder' => 'Vyberte sloupec',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Stáhnout vzorový soubor CSV',
            ],

            'import' => [
                'label' => 'Import',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import dokončen',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Stáhnout informace o chybném řádku|Stáhnout informace o chybných řádcích',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Nahraný CSV soubor je příliš velký',
            'body' => 'Nelze importovat více než 1 řádek najednou.|Nelze importovat více než :count řádků najednou.',
        ],

        'started' => [
            'title' => 'Zahájení importu',
            'body' => 'Váš import byl zahájen a na pozadí bude zpracován 1 řádek.|Váš import byl zahájen a na pozadí budou zpracovány řádky :count.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'chyba',
        'system_error' => 'Chyba systému, kontaktujte prosím podporu.',
    ],

];
