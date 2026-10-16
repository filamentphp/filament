<?php

return [

    'single' => [

        'label' => '還原',

        'modal' => [

            'heading' => '還原 :label',

            'actions' => [

                'restore' => [
                    'label' => '還原',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => '已還原資料',
            ],

        ],

    ],

    'multiple' => [

        'label' => '已還原所選的資料',

        'modal' => [

            'heading' => '已選擇 :label',

            'actions' => [

                'restore' => [
                    'label' => '還原',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => '已還原資料',
            ],

            'restored_partial' => [
                'title' => '已還原 :total 筆中的 :count 筆',
                'missing_authorization_failure_message' => '你沒有權限還原 :count 筆資料。',
                'missing_processing_failure_message' => '有 :count 筆資料無法還原。',
            ],

            'restored_none' => [
                'title' => '還原失敗',
                'missing_authorization_failure_message' => '你沒有權限還原 :count 筆資料。',
                'missing_processing_failure_message' => '有 :count 筆資料無法還原。',
            ],

        ],

    ],

];
