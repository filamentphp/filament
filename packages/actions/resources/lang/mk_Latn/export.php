<?php

return [

    'label' => 'Izvezi :label',

    'modal' => [

        'heading' => 'Izvezi :label',

        'form' => [

            'columns' => [

                'label' => 'Koloni',

                'actions' => [

                    'select_all' => [
                        'label' => 'Izberi site',
                    ],

                    'deselect_all' => [
                        'label' => 'Odberi site',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => ':column ovozmozeno',
                    ],

                    'label' => [
                        'label' => ':column etiketa',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Izvezi',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Izvezot e završen',

            'actions' => [

                'download_csv' => [
                    'label' => 'Prezemi .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Prezemi .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Izvezot e pregolem',
            'body' => 'Ne možete da izvezuvate povekje od 1 red odenaš.|Ne možete da izvezuvate povekje od :count redovi odenaš.',
        ],

        'no_columns' => [
            'title' => 'Nema izabrani koloni',
            'body' => 'Ve molime izberete barem edna kolona za izvoz.',
        ],

        'started' => [
            'title' => 'Izvezot e započnat',
            'body' => 'Vašiot izvoz započna i 1 red kje bide obraboten vo pozadina. Kje dobiete izvestuvanje so link za prezemanje koga kje završi.|Vašiot izvoz započna i :count redovi kje bidat obraboteni vo pozadina. Kje dobiete izvestuvanje so link za prezemanje koga kje završi.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
