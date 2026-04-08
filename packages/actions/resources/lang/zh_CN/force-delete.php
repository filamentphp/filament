<?php

return [

    'single' => [

        'label' => '强制删除',

        'modal' => [

            'heading' => '强制删除 :label',

            'actions' => [

                'delete' => [
                    'label' => '删除',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '记录已删除',
            ],

        ],

    ],

    'multiple' => [

        'label' => '强制删除已选项目',

        'modal' => [

            'heading' => '强制删除已选 :label',

            'actions' => [

                'delete' => [
                    'label' => '删除',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '记录已删除',
            ],

            'deleted_partial' => [
                'title' => '已删除 :count / :total',
                'missing_authorization_failure_message' => '您没有权限删除 :count 条记录。',
                'missing_processing_failure_message' => '无法删除 :count 条记录。',
            ],

            'deleted_none' => [
                'title' => '删除失败',
                'missing_authorization_failure_message' => '您没有权限删除 :count 条记录。',
                'missing_processing_failure_message' => '无法删除 :count 条记录。',
            ],

        ],

    ],

];
