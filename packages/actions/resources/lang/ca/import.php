<?php

return [

    'label' => 'Importar :label',

    'modal' => [

        'heading' => 'Importar :label',

        'form' => [

            'file' => [
                'label' => 'Arxiu',
                'placeholder' => 'Carregar un arxiu CSV',
                'rules' => [
                    'duplicate_columns' => '{0} El fitxer no ha de contenir més d\'un encapçalament de columna buit.|{1,*} El fitxer no ha de contenir encapçalaments de columna duplicats: :columns.',
                ],
            ],

            'columns' => [
                'label' => 'Columnes',
                'placeholder' => 'Seleccionar una columna',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Descarregar arxiu CSV d\'exemple',
            ],

            'import' => [
                'label' => 'Importar',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Importació completada',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Descarregar informació de la fila fallida|Descarregar informació de les files fallides',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'L\'arxiu CSV carregat és massa gran',
            'body' => 'No es pot importar més d\'una fila alhora.|No es poden importar més de :count files alhora.',
        ],

        'started' => [
            'title' => 'Importació iniciada',
            'body' => 'La vostra importació ha començat i es processarà 1 fila en segon pla.|La vostra importació ha començat i es processaran :count files en segon pla.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'Error del sistema, poseu-vos en contacte amb el servei d\'assistència.',
        'column_mapping_required_for_new_record' => 'La columna :attribute no s\'ha assignat a cap columna del fitxer, però és necessària per crear nous registres.',
    ],

];
