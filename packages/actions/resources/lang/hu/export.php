<?php

return [

    'label' => ':label exportálása',

    'modal' => [

        'heading' => ':label exportálása',

        'form' => [

            'columns' => [

                'label' => 'Oszlopok',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column bekapcsolva',
                    ],

                    'label' => [
                        'label' => ':column fejléc',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Exportálás',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'AZ exportálás befejeződött',

            'actions' => [

                'download_csv' => [
                    'label' => 'CSV letöltése',
                ],

                'download_xlsx' => [
                    'label' => 'XLSX letöltése',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Túl sok exportálandó sor',
            'body' => 'Nem exportálhatsz több mint 1 sort egyszerre.|Nem exportálhatsz több mint :count sor egyszerre.',
        ],

        'started' => [
            'title' => 'Az exportálás elkezdődött',
            'body' => 'Elkezdődött 1 exportálása a háttérben.|Elkezdődött :count sor exportálása a háttérben.',
        ],

    ],

    'file_name' => 'exportálás-:export_id-:model',

];
