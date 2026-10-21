<?php

return [

    'label' => '匯出 :label',

    'modal' => [

        'heading' => '匯出 :label',

        'form' => [

            'columns' => [

                'label' => '欄位',

                'actions' => [

                    'select_all' => [
                        'label' => '全選',
                    ],

                    'deselect_all' => [
                        'label' => '取消全選',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => ':column 開啟',
                    ],

                    'label' => [
                        'label' => ':column 標籤',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => '匯出',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => '匯出完成',

            'actions' => [

                'download_csv' => [
                    'label' => '下載 .csv 檔案',
                ],

                'download_xlsx' => [
                    'label' => '下載 .xlsx 檔案',
                ],

            ],

        ],

        'max_rows' => [
            'title' => '匯出的檔案過大',
            'body' => '你不能一次匯出超過 :count 行紀錄。',
        ],

        'no_columns' => [
            'title' => '未選擇任何欄位',
            'body' => '請至少選擇一個要匯出的欄位。',
        ],

        'started' => [
            'title' => '匯出開始',
            'body' => '你的匯出已經開始，將在背景處理共 :count 行。',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
