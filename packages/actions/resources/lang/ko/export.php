<?php

return [

    'label' => '내보내기',

    'modal' => [

        'heading' => ':label 내보내기',

        'form' => [

            'columns' => [

                'label' => '열',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column 활성화됨',
                    ],

                    'label' => [
                        'label' => ':column 라벨',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => '내보내기',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => '내보내기 완료',

            'actions' => [

                'download_csv' => [
                    'label' => '.csv 다운로드',
                ],

                'download_xlsx' => [
                    'label' => '.xlsx 다운로드',
                ],

            ],

        ],

        'max_rows' => [
            'title' => '내보내기가 너무 큽니다',
            'body' => '한 번에 1개 이상의 행을 내보낼 수 없습니다.|한 번에 :count개 이상의 행을 내보낼 수 없습니다.',
        ],

        'started' => [
            'title' => '내보내기 시작됨',
            'body' => '내보내기가 시작되었으며 1개의 행이 백그라운드에서 처리됩니다.|내보내기가 시작되었으며 :count개의 행이 백그라운드에서 처리됩니다.',
        ],

    ],

    'file_name' => '내보내기-:export_id-:model',

];
