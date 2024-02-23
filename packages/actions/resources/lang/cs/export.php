<?php

return [

    'label' => 'Exportovat :label',

    'modal' => [

        'heading' => 'Exportovat :label',

        'form' => [

            'columns' => [

                'label' => 'Sloupce',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column povoleno',
                    ],

                    'label' => [
                        'label' => ':column popisek',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Exportovat',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Export dokončen',

            'actions' => [

                'download_csv' => [
                    'label' => 'Stáhnout .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Stáhnout .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Export překračuje povolenou velikost',
            'body' => 'Není možné exportovat více než 1 řádek najednou.|Není možné exportovat více než :count řádků najednou.',
        ],

        'started' => [
            'title' => 'Export byl zahájen',
            'body' => 'Export byl zahájen a 1 řádek bude zpracován na pozadí.|Váš export byl zahájen a :count řádků bude zpracováno na pozadí.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
