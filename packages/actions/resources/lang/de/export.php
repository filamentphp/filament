<?php

return [

    'label' => 'Exportiere :label',

    'modal' => [

        'heading' => 'Exportiere :label',

        'form' => [

            'columns' => [

                'label' => 'Spalten',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column ist aktiviert',
                    ],

                    'label' => [
                        'label' => ':column Beschriftung',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Export',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Export abgeschlossen',

            'actions' => [

                'download_csv' => [
                    'label' => 'Lade .csv herunter',
                ],

                'download_xlsx' => [
                    'label' => 'Lade .xlsx herunter',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Der Export ist zu groß',
            'body' => 'Nicht mehr als 1 Zeile auf einmal exportieren.|Nicht mehr als :count Zeilen auf einmal exportieren.',
        ],

        'started' => [
            'title' => 'Export gestartet',
            'body' => 'Der Export wurde gestartet und 1 Zeile wird im Hintergrund verarbeitet.| Der Export wurde gestartet und :count Zeilen werden im Hintergrund verarbeitet.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
