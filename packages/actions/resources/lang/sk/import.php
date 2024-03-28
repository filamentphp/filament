<?php

return [

    'label' => 'Import :label',

    'modal' => [

        'heading' => 'Import :label',

        'form' => [

            'file' => [
                'label' => 'Súbor',
                'placeholder' => 'Nahrať CSV súbor',
            ],

            'columns' => [
                'label' => 'Stĺpce',
                'placeholder' => 'Vybrať stĺpce',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Stiahnuť vzorový CSV súbor',
            ],

            'import' => [
                'label' => 'Importovať',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import dokončený',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Stiahnuť informácie o chybnom riadku|Stiahnuť informácie o chybných riadkoch',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Nahraný CSV súbor je príliš veľký',
            'body' => 'Naraz nemôžete importovať viac ako 1 riadok.|Naraz nemôžete importovať viac ako :count riadkov.',
        ],

        'started' => [
            'title' => 'Import zahájený',
            'body' => 'Import bol zahájený a 1 riadok sa spracuje na pozadí.|Import bol zahájený a :count riadkov sa spracuje na pozadí.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'chyba',
        'system_error' => 'Chyba systému, prosím, kontaktujte podporu.',
    ],

];
