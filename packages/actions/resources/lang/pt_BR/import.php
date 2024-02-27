<?php

return [

    'label' => 'Importar :label',

    'modal' => [

        'heading' => 'Importar :label',

        'form' => [

            'file' => [
                'label' => 'Arquivo',
                'placeholder' => 'Enviar um arquivo CSV',
            ],

            'columns' => [
                'label' => 'Colunas',
                'placeholder' => 'Selecione uma coluna',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Baixar um arquivo CSV de exemplo',
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
                    'label' => 'Baixar informações sobre a linha que falhou|Baixar informações sobre as linhas que falharam',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'O arquivo CSV enviado é muito grande',
            'body' => 'Não é possível importar mais de 1 linha de uma vez.|Não é possível importar mais de :count linhas de uma vez.',
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
        'system_error' => 'Erro de sistema, por favor, entre em contato com o suporte.',
    ],

];
