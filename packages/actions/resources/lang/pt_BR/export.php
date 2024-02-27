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
                        'label' => ':column ativa',
                    ],

                    'label' => [
                        'label' => 'Rótulo para :column',
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

            'title' => 'Exportação completa',

            'actions' => [

                'download_csv' => [
                    'label' => 'Baixar .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Baixar .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'A exportação é muito grande',
            'body' => 'Não é possível exportar mais de 1 linha de uma vez.|Não é possível exportar mais de :count linhas de uma vez.',
        ],

        'started' => [
            'title' => 'Exportação iniciada',
            'body' => 'A exportação foi iniciada e 1 linha será processada em segundo plano.|A exportação foi iniciada e :count linhas serão processadas em segundo plano.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
