<?php

return [

    'single' => [

        'label' => '強制刪除',

        'modal' => [

            'heading' => '強制刪除 :label',

            'actions' => [

                'delete' => [
                    'label' => '刪除',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '已刪除資料',
            ],

        ],

    ],

    'multiple' => [

        'label' => '強制刪除所選的項目',

        'modal' => [

            'heading' => '強制刪除所選的 :label',

            'actions' => [

                'delete' => [
                    'label' => '刪除',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '已刪除資料',
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
