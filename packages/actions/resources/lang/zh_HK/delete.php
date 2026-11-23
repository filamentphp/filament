<?php

return [

    'single' => [

        'label' => '刪除',

        'modal' => [

            'heading' => '刪除 :label',

            'actions' => [

                'delete' => [
                    'label' => '刪除',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '已刪除',
            ],

        ],

    ],

    'multiple' => [

        'label' => '刪除所選的項目',

        'modal' => [

            'heading' => '刪除所選的 :label',

            'actions' => [

                'delete' => [
                    'label' => '刪除',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '已刪除',
            ],

            'deleted_partial' => [
                'title' => '已刪除 :total 筆中的 :count 筆',
                'missing_authorization_failure_message' => '你沒有權限刪除 :count 筆資料。',
                'missing_processing_failure_message' => '有 :count 筆資料無法刪除。',
            ],

            'deleted_none' => [
                'title' => '刪除失敗',
                'missing_authorization_failure_message' => '你沒有權限刪除 :count 筆資料。',
                'missing_processing_failure_message' => '有 :count 筆資料無法刪除。',
            ],

        ],

    ],

];
