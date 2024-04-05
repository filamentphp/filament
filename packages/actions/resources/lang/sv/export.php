<?php

return [

    'label' => 'Exportera :label',

    'modal' => [

        'heading' => 'Exportera :label',

        'form' => [

            'columns' => [

                'label' => 'Kolumner',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column aktiverad',
                    ],

                    'label' => [
                        'label' => ':column namn',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Exportera',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Export slutförd',

            'actions' => [

                'download_csv' => [
                    'label' => 'Ladda ner .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Ladda ner .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Exporten är för stor',
            'body' => 'Du kan inte exportera fler än 1 rad åt gången.|Du kan inte exportera fler än :count rader åt gången.',
        ],

        'started' => [
            'title' => 'Exporten startades',
            'body' => 'Din export har börjat och 1 rad kommer att bearbetas i bakgrunden.|Din export har börjat och :count rader kommer att bearbetas i bakgrunden.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
