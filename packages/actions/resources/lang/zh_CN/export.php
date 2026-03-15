<?php

return [

    'label' => '导出 :label',

    'modal' => [

        'heading' => '导出 :label',

        'form' => [

            'columns' => [

                'label' => '列',

                'actions' => [

                    'select_all' => [
                        'label' => '全选',
                    ],

                    'deselect_all' => [
                        'label' => '取消全选',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => '启用 :column',
                    ],

                    'label' => [
                        'label' => ':column 标签',
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

            'title' => '导出完成',

            'actions' => [

                'download_csv' => [
                    'label' => '下载 .csv',
                ],

                'download_xlsx' => [
                    'label' => '下载 .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => '导出数据过多',
            'body' => '一次最多只能导出 1 行记录。|一次最多只能导出 :count 行记录。',
        ],

        'no_columns' => [
            'title' => '未选择列',
            'body' => '请至少选择一列进行导出。',
        ],

        'started' => [
            'title' => '导出已开始',
            'body' => '导出已开始，将在后台处理 1 行数据。完成后您将收到带有下载链接的通知。|导出已开始，将在后台处理 :count 行数据。完成后您将收到带有下载链接的通知。',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
