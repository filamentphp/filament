<?php

return [

    'single' => [

        'label' => '恢复',

        'modal' => [

            'heading' => '恢复 :label',

            'actions' => [

                'restore' => [
                    'label' => '恢复',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => '记录已恢复',
            ],

        ],

    ],

    'multiple' => [

        'label' => '恢复已选项目',

        'modal' => [

            'heading' => '恢复已选 :label',

            'actions' => [

                'restore' => [
                    'label' => '恢复',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => '记录已恢复',
            ],

            'restored_partial' => [
                'title' => '已恢复 :count / :total',
                'missing_authorization_failure_message' => '您没有权限恢复 :count 条记录。',
                'missing_processing_failure_message' => '无法恢复 :count 条记录。',
            ],

            'restored_none' => [
                'title' => '恢复失败',
                'missing_authorization_failure_message' => '您没有权限恢复 :count 条记录。',
                'missing_processing_failure_message' => '无法恢复 :count 条记录。',
            ],

        ],

    ],

];
