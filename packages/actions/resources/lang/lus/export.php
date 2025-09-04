<?php

return [

    'label' => ':Label export na',

    'modal' => [

        'heading' => ':Label export na',

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
            'title' => 'Export hi a lian lutuk',
            'body' => 'Vawikhatah row 1 ai a tam a export theiloh.|Vawikhatah rows :count ai a tam a export theiloh',
        ],

        'started' => [
            'title' => 'Export started',
            'body' => 'I export a intan a, row 1 background ah a insiam ang. A zawh hunah download link awmna nen notification i dawng ang.|I export a intan a, rows :count background ah a insiam ang. A zawh hunah download link awmna nen notification i dawng ang.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
