<?php

return [

    'label' => 'Импорт :label',

    'modal' => [

        'heading' => 'Импорт :label',

        'form' => [

            'file' => [
                'label' => 'Файл',
                'placeholder' => 'Загрузить CSV-файл',
            ],

            'columns' => [
                'label' => 'Столбцы',
                'placeholder' => 'Выберите столбец',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'Скачать пример CSV-файла',
            ],

            'import' => [
                'label' => 'Импорт',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Импорт завершен',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'Загрузить информацию о неудавшейся строке|Загрузить информацию о неудавшейся строке',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Загруженный файл CSV слишком велик.',
            'body' => 'Вы не можете импортировать более 1 строки одновременно.|Вы не можете импортировать более :count строк одновременно.',
        ],

        'started' => [
            'title' => 'Импорт начался',
            'body' => 'Ваш импорт начался, и 1 строка будет обработана в фоновом режиме.|Ваш импорт начался, и :count строк будет обрабатываться в фоновом режиме.',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'Ошибка',
        'system_error' => 'Системная ошибка, обратитесь в службу поддержки.',
    ],

];
