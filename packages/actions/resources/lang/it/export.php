<?php

return [

    'label' => 'Esporta :label',

    'modal' => [

        'heading' => 'Esporta :label',

        'form' => [

            'columns' => [

                'label' => 'Colonne',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column abilitata',
                    ],

                    'label' => [
                        'label' => ':column etichetta',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Esporta',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Esportazione completata',

            'actions' => [

                'download_csv' => [
                    'label' => 'Scarica .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Scarica .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'L\'esportazione è troppo grande',
            'body' => 'Non puoi esportare più di 1 riga alla volta.|Non puoi esportare più di :count righe alla volta.',
        ],

        'started' => [
            'title' => 'Esportazione avviata',
            'body' => 'L\'esportazione è iniziata e 1 riga verrà elaborata in background.|L\'esportazione è iniziata e :count righe verranno elaborate in background.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
