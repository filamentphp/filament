<?php

return [

    'label' => ':label Import',

    'modal' => [

        'heading' => ':label Import',

        'form' => [

            'file' => [
                'label' => 'Datei',
                'placeholder' => 'CSV Datei hochladen',
            ],

            'columns' => [
                'label' => 'Spalten',
                'placeholder' => 'Wähle Spalte',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Lade Beispiel CSV herunter',
            ],

            'import' => [
                'label' => 'Importieren',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Import abgeschlossen',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Informationen über die fehlgeschlagene Zeile herunterladen|Informationen über die fehlgeschlagenen Zeilen herunterladen',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'CSV Datei ist zu groß',
            'body' => 'Nicht mehr als eine Zeile auf einmal importieren.|Nicht mehr als :count Zeilen importieren.',
        ],

        'started' => [
            'title' => 'Import gestartet',
            'body' => 'Der Import ist gestartet und eine Zeile wird im Hintergrund verarbeitet.|Der Import hat begonnen und :count Zeilen werden im Hintergrund verarbeitet.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'Fehler',
        'system_error' => 'Systemfehler, bitte Supportanfrage stellen.',
    ],

];
