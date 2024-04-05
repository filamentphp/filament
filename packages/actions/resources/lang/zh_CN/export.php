<?php

return [

    'label' => '导出:label',

    'modal' => [

        'heading' => '导出:label',

        'form' => [

            'columns' => [

                'label' => '字段',

                'form' => [

                    'is_enabled' => [
                        'label' => '开启:column',
                    ],

                    'label' => [
                        'label' => ':column标签',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => '导出',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => '导出完毕',

            'actions' => [

                'download_csv' => [
                    'label' => '下载.csv',
                ],

                'download_xlsx' => [
                    'label' => '下载.xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => '导出的文件过大',
            'body' => '不能一次导出超过的行记录。|不能一次导出超过:count 行记录。',
        ],

        'started' => [
            'title' => '开始导出',
            'body' => '你的导出已经开始，将在后台处理:count 行。',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
