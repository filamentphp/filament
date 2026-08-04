<?php

return [

    'label' => 'Содироти :label',

    'modal' => [

        'heading' => 'Содироти :label',

        'form' => [

            'columns' => [

                'label' => 'Сутунҳо',

                'actions' => [

                    'select_all' => [
                        'label' => 'Интихоби ҳама',
                    ],

                    'deselect_all' => [
                        'label' => 'Бекор кардани интихоб',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => ':column фаъол аст',
                    ],

                    'label' => [
                        'label' => 'Номи :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Содирот',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Содирот анҷом ёфт',

            'actions' => [

                'download_csv' => [
                    'label' => 'Боргирии .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Боргирии .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Ҳаҷми содирот хеле калон аст',
            'body' => 'Шумо наметавонед дар як вақт зиёда аз 1 сатрро содир кунед.|Шумо наметавонед дар як вақт зиёда аз :count сатрро содир кунед.',
        ],

        'no_columns' => [
            'title' => 'Сутунҳо интихоб нашудаанд',
            'body' => 'Лутфан барои содирот ҳадди ақал як сутунро интихоб кунед.',
        ],

        'started' => [
            'title' => 'Содирот оғоз шуд',
            'body' => 'Содирот оғоз шуд ва 1 сатр дар замина коркард мешавад.|Содирот оғоз шуд ва :count сатр дар замина коркард мешавад.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
