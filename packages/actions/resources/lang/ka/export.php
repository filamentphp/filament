<?php

return [

    'label' => 'Export :label',

    'modal' => [

        'heading' => 'Export :label',

        'form' => [

            'columns' => [

                'label' => 'Columns',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column enabled',
                    ],

                    'label' => [
                        'label' => ':column label',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Export',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Export completed',

            'actions' => [

                'download_csv' => [
                    'label' => 'Download .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Download .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Export is too large',
            'body' => 'You may not export more than 1 row at once.|You may not export more than :count rows at once.',
        ],

        'started' => [
            'title' => 'Export started',
            'body' => 'Your export has begun and 1 row will be processed in the background. You will receive a notification with the download link when it is complete.|Your export has begun and :count rows will be processed in the background. You will receive a notification with the download link when it is complete.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
