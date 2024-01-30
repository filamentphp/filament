<?php

return [

    'label' => 'Экспорт :label',

    'modal' => [

        'heading' => 'Экспорт :label',

        'form' => [

            'columns' => [

                'label' => 'Столбцы',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column включено',
                    ],

                    'label' => [
                        'label' => ':column метка',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Экспорт',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Экспорт завершен',

            'actions' => [

                'download_csv' => [
                    'label' => 'Скачать .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Скачать .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Экспорт слишком велик',
            'body' => 'Вы не можете экспортировать более 1 строки одновременно.|Вы не можете экспортировать более :count строк одновременно.',
        ],

        'started' => [
            'title' => 'Экспорт начался',
            'body' => 'Ваш экспорт начался, и 1 строка будет обработана в фоновом режиме.|Ваш экспорт начался, и :count строк будет обработан в фоновом режиме.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
