<?php

return [

    'label' => '匯入:label',

    'modal' => [

        'heading' => '匯入:label',

        'form' => [

            'file' => [
                'label' => '檔案',
                'placeholder' => '上傳一個 CSV 檔案',
            ],

            'columns' => [
                'label' => '欄位',
                'placeholder' => '選擇一個欄位',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => '下載一個 CSV 模板',
            ],

            'import' => [
                'label' => '匯入',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => '匯入完成',

            'actions' => [
                'download_failed_rows_csv' => [
                    'label' => '下載匯入失敗的紀錄|下載匯入失敗的紀錄',
                ],

            ],

        ],

        'max_rows' => [
            'title' => '上傳的 CSV 檔案過大',
            'body' => '你不能一次匯入超過 1 行紀錄。|你不能一次匯入超過 :count 行紀錄。',
        ],

        'started' => [
            'title' => '匯入開始',
            'body' => '你的匯入已經開始，將在後台處理共 1 行。|你的匯入已經開始，將在後台處理共 :count 行。',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => '錯誤',
        'system_error' => '系統錯誤，請聯繫官方。',
    ],

];
