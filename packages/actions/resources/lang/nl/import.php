<?php

return [

    'label' => ':Label importeren',

    'modal' => [

        'heading' => ':Label importeren',

        'form' => [

            'file' => [

                'label' => 'Bestand',

                'placeholder' => 'Upload een CSV-bestand',

                'rules' => [
                    'duplicate_columns' => '{0} Het bestand mag niet meer dan één lege kolomkop bevatten.|{1,*} Het bestand mag geen dubbele kolomkoppen bevatten: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Kolommen',
                'placeholder' => 'Selecteer een kolom',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Voorbeeld CSV-bestand downloaden',
            ],

            'import' => [
                'label' => 'Importeren',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Importeren voltooid',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Informatie over de mislukte rij downloaden|Informatie over de mislukte rijen downloaden',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Geüploade CSV-bestand is te groot',
            'body' => 'Je mag niet meer dan 1 rij tegelijk importeren.|Je mag niet meer dan :count rijen tegelijk importeren.',
        ],

        'started' => [
            'title' => 'Importeren gestart',
            'body' => 'Je import is begonnen en 1 rij wordt op de achtergrond verwerkt.|Je import is begonnen en :count rijen worden op de achtergrond verwerkt.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-voorbeeld',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-mislukte-rijen',
        'error_header' => 'fout',
        'system_error' => 'Systeemfout, neem contact op met ondersteuning.',
    ],

];
