<?php

return [

    'label' => 'Izvozi :label',

    'modal' => [

        'heading' => 'Izvozi :label',

        'form' => [

            'columns' => [

                'label' => 'Stolpci',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column omogočeno',
                    ],

                    'label' => [
                        'label' => 'Oznaka :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Izvozi',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Izvoz zaključen',

            'actions' => [

                'download_csv' => [
                    'label' => 'Prenesi .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Prenesi .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Izvoz je prevelik',
            'body' => 'Naenkrat lahko izvozite največ 1 vrstico.|Naenkrat lahko izvozite največ :count vrstic.',
        ],

        'started' => [
            'title' => 'Izvoz se je začel',
            'body' => 'Vaš izvoz se je začel in 1 vrstica bo obdelana v ozadju. Ko bo končano, boste prejeli obvestilo s povezavo za prenos.|Vaš izvoz se je začel in :count vrstic bo obdelanih v ozadju. Ko bo končano, boste prejeli obvestilo s povezavo za prenos.',
        ],

    ],

    'file_name' => 'izvoz-:export_id-:model',

];
