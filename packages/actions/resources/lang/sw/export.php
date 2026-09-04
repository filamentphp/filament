<?php

return [

    'label' => 'Hamisha :label',

    'modal' => [
        'heading' => 'Hamisha :label',

        'form' => [
            'columns' => [
                'label' => 'Safu wima',

                'actions' => [
                    'select_all' => [
                        'label' => 'Chagua vyote',
                    ],

                    'deselect_all' => [
                        'label' => 'Ondoa uchaguzi wote',
                    ],
                ],

                'form' => [
                    'is_enabled' => [
                        'label' => ':column imewashwa',
                    ],

                    'label' => [
                        'label' => 'Lebo ya :column',
                    ],
                ],
            ],
        ],

        'actions' => [
            'export' => [
                'label' => 'Hamisha',
            ],
        ],
    ],

    'notifications' => [
        'completed' => [
            'title' => 'Kuhamisha Kumekamilika',

            'actions' => [
                'download_csv' => [
                    'label' => 'Pakua .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Pakua .xlsx',
                ],
            ],
        ],

        'max_rows' => [
            'title' => 'Kuhamisha ni kubwa mno',
            'body' => 'Huwezi kuhamisha zaidi ya safu 1 kwa wakati mmoja.|Huwezi kuhamisha zaidi ya safu :count kwa wakati mmoja.',
        ],

        'no_columns' => [
            'title' => 'Hakuna safu wima zilizochaguliwa',
            'body' => 'Tafadhali chagua angalau safu wima moja ya kuhamisha.',
        ],

        'started' => [
            'title' => 'Kuhamisha Kumeanza',
            'body' => 'Kuhamisha kwako kumeanza na safu 1 itashughulikiwa nyuma ya pazia. Utapokea taarifa yenye kiungo cha kupakia inapokamilika.|Kuhamisha kwako kumeanza na safu :count zitashughulikiwa nyuma ya pazia. Utapokea taarifa yenye kiungo cha kupakia inapokamilika.',
        ],
    ],

    'file_name' => 'export-:export_id-:model',

];
