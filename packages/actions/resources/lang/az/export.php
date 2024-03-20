<?php

return [

    'label' => 'İxrac :label',

    'modal' => [

        'heading' => 'İxrac :label',

        'form' => [

            'columns' => [

                'label' => 'Sütunlar',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column aktiv',
                    ],

                    'label' => [
                        'label' => ':column etiketi',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'İxrac',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'İxrac tamamlandı',

            'actions' => [

                'download_csv' => [
                    'label' => '.csv olaraq endir',
                ],

                'download_xlsx' => [
                    'label' => '.xlsx olaraq endir',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'İxrac çox böyükdür',
            'body' => 'Bir dəfədə 1 sətirdən çox ixrac edə bilməzsiniz.| Bir dəfədə :count sətirdən çox ixrac edə bilməzsiniz.',
        ],

        'started' => [
            'title' => 'İxrac başladı',
            'body' => 'İxrac başladı və 1 sətir arxa planda işlənəcək.|İxrac başladı və :count sətir arxa planda işlənəcək.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
