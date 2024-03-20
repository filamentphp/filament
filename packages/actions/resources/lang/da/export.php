<?php

return [

    'label' => 'Eksporter :label',

    'modal' => [

        'heading' => 'Eksporter :label',

        'form' => [

            'columns' => [

                'label' => 'Kolonner',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column aktiveret',
                    ],

                    'label' => [
                        'label' => ':column etiket',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Eksporter',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Eksporter fuldført',

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
            'title' => 'Eksporten er for stor',
            'body' => 'Du må ikke eksportere mere end 1 række på én gang.|Du må ikke eksportere mere end :count rækker på én gang.',
        ],

        'started' => [
            'title' => 'Eksport er started',
            'body' => 'Din eksport er begyndt, og 1 række vil blive behandlet i baggrunden.|Din eksport er begyndt, og :count rækker vil blive behandlet i baggrunden.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
