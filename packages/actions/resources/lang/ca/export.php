<?php

return [

    'label' => 'Exportar :label',

    'modal' => [

        'heading' => 'Exportar :label',

        'form' => [

            'columns' => [

                'label' => 'Columnes',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column habilitada',
                    ],

                    'label' => [
                        'label' => 'etiqueta per a :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Exportar',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Exportació completada',

            'actions' => [

                'download_csv' => [
                    'label' => 'Descarregar .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Descarregar .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'L\'exportació és massa gran',
            'body' => 'No es pot exportar més d\'una fila alhora.|No es poden exportar més de :count files alhora.',
        ],

        'started' => [
            'title' => 'Exportació iniciada',
            'body' => 'L\'exportació ha començat i es processarà una fila en segon pla.|L\'exportació ha començat i es processaran :count files en segon pla.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
