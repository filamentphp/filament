<?php

return [

    'label' => ':Label dah chhuahna',

    'modal' => [

        'heading' => ':Label dah chhuahna',

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
                'label' => 'Dah chhuahna',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Dah chhuah a zo e.',

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
            'title' => 'Dah chhuah tum hi a lian lutuk',
            'body' => 'Vawikhatah row 1 ai a tam a dah chhuah theiloh.|Vawikhatah rows :count ai a tam a dah chhuah theiloh',
        ],

        'started' => [
            'title' => 'Dah chhuah a intan e',
            'body' => 'I dah chhuah a intan a, row 1 background ah a insiam ang. A zawh hunah download link awmna nen hriattîrna i dawng ang.|I dahchhuah a intan a, rows :count background ah a insiam ang. A zawh hunah download link awmna nen hriattîrna i dawng ang.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
