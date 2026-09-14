<?php

return [

    'label' => 'Import :label',

    'modal' => [

        'heading' => 'Import :label',

        'form' => [

            'file' => [
                'label' => 'Soubor',
                'placeholder' => 'Nahrát soubor CSV',
                'rules' => [
                    'duplicate_columns' => '{0} Soubor nesmí obsahovat více než jednu prázdnou hlavičku sloupce.|{1,*} Soubor nesmí obsahovat duplicitní hlavičky sloupců: :columns.',
                ],
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
                    'label' => '{1} Stáhnout informace o chybném řádku|[0,*] Stáhnout informace o chybných řádcích',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Nahraný soubor CSV je příliš velký',
            'body' => 'Nelze importovat více než 1 řádek najednou.|Nelze importovat více než :count řádky najednou.|Nelze importovat více než :count řádků najednou.',
        ],

        'started' => [
            'title' => 'Zahájení importu',
            'body' => 'Import byl zahájen a na pozadí bude zpracován 1 řádek.|Import byl zahájen a na pozadí budou zpracovány :count řádky.|Import byl zahájen a na pozadí bude zpracováno :count řádků.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'chyba',
        'system_error' => 'Chyba systému, kontaktujte prosím podporu.',
        'column_mapping_required_for_new_record' => 'Sloupec :attribute nebyl přiřazen k žádnému sloupci v souboru, ale je vyžadován pro vytvoření nových záznamů.',
    ],

];
