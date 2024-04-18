<?php

return [

    'label' => 'Eksportuoti :label',

    'modal' => [

        'heading' => 'Eksportuoti :label',

        'form' => [

            'columns' => [

                'label' => 'Stulpeliai',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column įjungtas',
                    ],

                    'label' => [
                        'label' => ':column label',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Eksportuoti',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Eksportas baigtas',

            'actions' => [

                'download_csv' => [
                    'label' => 'Atsisiųsti .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Atsisiųsti .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Eksportas per didelis',
            'body' => 'Negalite eksportuoti daugiau nei 1 eilutės vienu metu.|Negalite eksportuoti daugiau nei :count eilučių vienu metu.',
        ],

        'started' => [
            'title' => 'Eksportas pradėtas',
            'body' => 'Eksportas pradėtas ir 1 eilutė bus apdorota fone.|Eksportas pradėtas ir :count eilutės bus apdorotos fone.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
