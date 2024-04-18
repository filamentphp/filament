<?php

return [

    'label' => 'Importér :label',

    'modal' => [

        'heading' => 'Importér :label',

        'form' => [

            'file' => [
                'label' => 'Fil',
                'placeholder' => 'Last opp en CSV fil',
            ],

            'columns' => [
                'label' => 'Kolonner',
                'placeholder' => 'Velg en kolonne',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Last ned en CSV-eksempel fil',
            ],

            'import' => [
                'label' => 'Importér',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Importering gjennomført',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Last ned informasjon om raden som feilet|Last ned informasjon om rader som feilet',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Opplastet CSV fil er for stor',
            'body' => 'Du kan ikke importere mer enn 1 rad av gangen.|Du kan ikke importere mer enn :count rader av gangen.',
        ],

        'started' => [
            'title' => 'Importering startet',
            'body' => 'Din importering har startet og 1 rad vil bli behandlet i bakgrunnen.|Din importering har startet og :count rader vil bli behandlet i bakgrunnen.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-eksempel',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-rader-feilet',
        'error_header' => 'feil',
        'system_error' => 'Systemfeil, vennligst kontakt support.',
    ],

];
