<?php

return [

    'label' => '导入:label',

    'modal' => [

        'heading' => '导入:label',

        'form' => [

            'file' => [
                'label' => '文件',
                'placeholder' => '上传一个 CSV 文件',
            ],

            'columns' => [
                'label' => '栏位',
                'placeholder' => '选择一个栏位',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => '下载一个 CSV 模板',
            ],

            'import' => [
                'label' => '导入',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => '导入完成',

            'actions' => [
                'download_failed_rows_csv' => [
                    'label' => '下载导入失败的记录|下载导入失败的记录',
                ],

            ],

        ],

        'max_rows' => [
            'title' => '上传的 CSV 文件过大',
            'body' => '你不能够一次性导入超过 1 行记录。|你不能够一次性导入超过 :count 行记录。',
        ],

        'started' => [
            'title' => '导入开始',
            'body' => '你的导入已经开始，共 1 行将在后台处理。|你的导入已经开始，共 :count 行将在后台处理。',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => '错误',
        'system_error' => '系统错误，请联系支持。',
    ],

];
