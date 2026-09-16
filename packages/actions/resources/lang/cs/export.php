<?php

return [

    'label' => 'Exportovat :label',

    'modal' => [

        'heading' => 'Exportovat :label',

        'form' => [

            'columns' => [

                'label' => 'Sloupce',

                'actions' => [

                    'select_all' => [
                        'label' => 'Vybrat vše',
                    ],

                    'deselect_all' => [
                        'label' => 'Zrušit výběr všeho',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => 'Povolit sloupec :column',
                    ],

                    'label' => [
                        'label' => 'Popisek sloupce :column',
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
            'body' => 'Není možné exportovat více než 1 řádek najednou.|Není možné exportovat více než :count řádky najednou.|Není možné exportovat více než :count řádků najednou.',
        ],

        'no_columns' => [
            'title' => 'Nejsou vybrány žádné sloupce',
            'body' => 'Vyberte prosím alespoň jeden sloupec k exportu.',
        ],

        'started' => [
            'title' => 'Export byl zahájen',
            'body' => 'Export byl zahájen a na pozadí bude zpracován 1 řádek.|Export byl zahájen a na pozadí budou zpracovány :count řádky.|Export byl zahájen a na pozadí bude zpracováno :count řádků.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
