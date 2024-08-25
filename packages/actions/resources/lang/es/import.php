<?php

return [

    'label' => 'Importar :label',

    'modal' => [

        'heading' => 'Importar :label',

        'form' => [

            'file' => [

                'label' => 'Archivo',

                'placeholder' => 'Cargar un archivo CSV',

                'rules' => [
                    'duplicate_columns' => '{0} El archivo no debe contener más de un encabezado de columna vacío.|{1,*} El archivo no debe contener encabezados de columna duplicados: :columns.',
                ],

            ],

            'columns' => [
                'label' => 'Columnas',
                'placeholder' => 'Seleccionar una columna',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Descargar archivo CSV de ejemplo',
            ],

            'import' => [
                'label' => 'Importar',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Importación completada',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Descargar información de la fila fallida|Descargar información de las filas fallidas',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'El archivo CSV cargado es demasiado grande',
            'body' => 'No se puede importar más de una fila a la vez.|No se pueden importar más de :count filas a la vez.',
        ],

        'started' => [
            'title' => 'Importación iniciada',
            'body' => 'Su importación ha comenzado y se procesará 1 fila en segundo plano.|Su importación ha comenzado y se procesarán :count filas en segundo plano.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'Error del sistema, póngase en contacto con el servicio de asistencia.',
        'column_mapping_required_for_new_record' => 'La columna :attribute no se asignó a una columna del archivo, pero esto es necesario para crear nuevos registros.',
    ],

];
