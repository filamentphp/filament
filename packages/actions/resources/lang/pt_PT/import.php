<?php

return [

    'label' => 'Importar :label',

    'modal' => [

        'heading' => 'Importar :label',

        'form' => [

            'file' => [
                'label' => 'Ficheiro',
                'placeholder' => 'Carregar um ficheiro CSV',
            ],

            'columns' => [
                'label' => 'Colunas',
                'placeholder' => 'Seleccione uma coluna',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Descarregar um ficheiro CSV de exemplo',
            ],

            'import' => [
                'label' => 'Importar',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Importação completa',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Descarregar informação sobre a falha na linha|Descarregar informação sobre as falhas nas linhas',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'O ficheiro CSV carregado é demasiado grande',
            'body' => 'Não é possível importar mais de uma linha de uma só vez.|Não é possível importar mais de :count linhas de uma só vez.',
        ],

        'started' => [
            'title' => 'Importação iniciada',
            'body' => 'A importação foi iniciada e 1 linha será processada em segundo plano.|A importação foi iniciada e :count linhas serão processadas em segundo plano.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'erro',
        'system_error' => 'Erro de sistema, por favor, contacte o suporte técnico.',
    ],

];
