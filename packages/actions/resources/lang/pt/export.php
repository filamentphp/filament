<?php

return [

    'label' => 'Exportar :label',

    'modal' => [

        'heading' => 'Exportar :label',

        'form' => [

            'columns' => [

                'label' => 'Colunas',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column activa',
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

            'title' => 'Exportação terminada',

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
            'title' => 'A exportação é demasiado grande',
            'body' => 'Não é possível exportar mais de um registo de uma só vez.|Não é possível exportar mais de :count registos de uma só vez.',
        ],

        'started' => [
            'title' => 'Exportação iniciada',
            'body' => 'A exportação foi iniciada e 1 registo será processado em segundo plano.|A exportação foi iniciada e :count registos serão processados em segundo plano.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
