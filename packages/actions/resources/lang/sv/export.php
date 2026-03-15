<?php

return [

    'label' => 'Exportera :label',

    'modal' => [

        'heading' => 'Exportera :label',

        'form' => [

            'columns' => [

                'label' => 'Kolumner',

                'actions' => [

                    'select_all' => [
                        'label' => 'Markera alla',
                    ],

                    'deselect_all' => [
                        'label' => 'Avmarkera alla',
                    ],

                ],

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

        'no_columns' => [
            'title' => 'Inga kolumner valda',
            'body' => 'Välj minst en kolumn att exportera.',
        ],

        'started' => [
            'title' => 'Exporten startades',
            'body' => 'Din export har börjat och 1 rad kommer att bearbetas i bakgrunden. Du får en notis med en nedladdningslänk när den är slutförd.|Din export har börjat och :count rader kommer att bearbetas i bakgrunden. Du får en notis med en nedladdningslänk när den är slutförd.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
