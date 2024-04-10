<?php

return [

    'label' => 'Експорт :label',

    'modal' => [

        'heading' => 'Експорт :label',

        'form' => [

            'columns' => [

                'label' => 'Стовпці',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column увімкнено',
                    ],

                    'label' => [
                        'label' => ':column мітка',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Експорт',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Експорт завершено',

            'actions' => [

                'download_csv' => [
                    'label' => 'Завантажити .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Завантажити .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Експорт занадто великий',
            'body' => 'Ви не можете експортувати більше 1 рядка одночасно.|Ви не можете експортувати більше :count рядків одночасно.',
        ],

        'started' => [
            'title' => 'Експорт розпочався',
            'body' => 'Ваш експорт почався, і 1 рядок буде оброблено у фоновому режимі.|Ваш експорт почався, і :count рядків буде оброблено у фоновому режимі.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
