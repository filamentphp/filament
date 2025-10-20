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
                        'label' => ':column aktivert',
                    ],

                    'label' => [
                        'label' => ':column etikett',
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

            'title' => 'Eksportering gjennomført',

            'actions' => [

                'download_csv' => [
                    'label' => 'Last ned .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Last ned .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Eksporteringen er for stor',
            'body' => 'Du kan ikke eksporterer mer enn 1 rap av gangen.|Du kan ikke eksportere mer enn :count rader av gangen.',
        ],

        'started' => [
            'title' => 'Eksportering startet',
            'body' => 'Din eksportering har startet og 1 rad vil bli behandlet i bakgrunnen.|Din eksportering har startet og :count rader vil bli behandlet i bakgrunnen.',
        ],

    ],

    'file_name' => 'eksport-:export_id-:model',

];
