<?php

return [
    'label' => 'Uvezi :label',

    'modal' => [

        'heading' => 'Uvezi :label',

        'form' => [

            'file' => [

                'label' => 'File',

                'placeholder' => 'Prenesi CSV datoteku',
                // Ovdje nisam bio siguran je li moramo i to pravilo prevesti
                'rules' => [
                    'duplicate_columns' => '{0} The file must not contain more than one empty column header.|{1,*} The file must not contain duplicate column headers: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Stupac',
                'placeholder' => 'Odaberi stupac',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Preuzmi primjer CSV datoteke',
            ],

            'import' => [
                'label' => 'Uvoz',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Uvoz je završen',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Preuzmi informaciju o neuspjelom pokušaju preuzimanja retka|Preuzmi informaciju o neuspjelom pokušaju preuzimanja redaka',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Poslana CSV datoteka je prevelika',
            'body' => '{1} Ne možeš uvesti više od jednog reda odjednom.|[2,4] Ne možeš uvesti više od :count reda odjednom.|[5,*] Ne možeš uvesti više od :count redova odjednom.',
        ],

        'started' => [
            'title' => 'Uvoz je započeo',
            'body' => '{1} Uvoz je započeo i jedan red će se obraditi u pozadini.|[2,4] Uvoz je započeo i :count reda će se obraditi u pozadini.|[5,*] Uvoz je započeo i :count redova će se obraditi u pozadini.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'System error, please contact support.',
        'column_mapping_required_for_new_record' => 'The :attribute column was not mapped to a column in the file, but it is required for creating new records.',
    ],

];
