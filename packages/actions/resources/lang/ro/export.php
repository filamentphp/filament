<?php

return [

    'label' => 'Exportă :label',

    'modal' => [

        'heading' => 'Exportă :label',

        'form' => [

            'columns' => [

                'label' => 'Coloane',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column activat',
                    ],

                    'label' => [
                        'label' => 'Eticheta :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Exportă',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Export complet',

            'actions' => [

                'download_csv' => [
                    'label' => 'Descarcă .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Descarcă .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Exportul este prea mare',
            'body' => 'Nu puteți exporta mai mult de 1 rând odată.|Nu puteți exporta mai mult de :count rânduri odată',
        ],

        'started' => [
            'title' => 'Exportul a început',
            'body' => 'Exportul dvs. a început și 1 rând va fi procesat în fundal.|Exportul dvs. a început și :count rânduri vor fi procesate în fundal.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
