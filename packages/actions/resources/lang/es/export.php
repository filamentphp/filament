<?php

return [

    'label' => 'Exportar :label',

    'modal' => [

        'heading' => 'Exportar :label',

        'form' => [

            'columns' => [

                'label' => 'Columnas',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column habilitada',
                    ],

                    'label' => [
                        'label' => 'etiqueta para :column',
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

            'title' => 'Exportación completada',

            'actions' => [

                'download_csv' => [
                    'label' => 'Descargar .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Descargar .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'La exportación es demasiado grande',
            'body' => 'No se puede exportar más de 1 fila a la vez.|No se pueden exportar más de :count filas a la vez.',
        ],

        'started' => [
            'title' => 'Exportación iniciada',
            'body' => 'Su exportación ha comenzado y se procesará 1 fila en segundo plano.|Su exportación ha comenzado y se procesarán :count filas en segundo plano.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
