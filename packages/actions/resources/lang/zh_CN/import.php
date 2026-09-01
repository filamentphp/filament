<?php

return [

    'label' => '导入 :label',

    'modal' => [

        'heading' => '导入 :label',

        'form' => [

            'file' => [

                'label' => '文件',

                'placeholder' => '上传 CSV 文件',

                'rules' => [
                    'duplicate_columns' => '{0} 文件不能包含多个空列标题。|{1,*} 文件不能包含重复的列标题：:columns。',
                ],

            ],

            'columns' => [
                'label' => '列',
                'placeholder' => '选择一列',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => '下载示例 CSV 模板',
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
            'body' => '一次最多只能导入 1 行记录。|一次最多只能导入 :count 行记录。',
        ],

        'started' => [
            'title' => '导入已开始',
            'body' => '导入已开始，将在后台处理 1 行数据。|导入已开始，将在后台处理 :count 行数据。',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => '错误',
        'system_error' => '系统错误，请联系技术支持。',
        'column_mapping_required_for_new_record' => ':attribute 列未映射到文件中的列，但创建新记录时需要此列。',
    ],

];
