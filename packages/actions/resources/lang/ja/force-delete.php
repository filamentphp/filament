<?php

return [

    'single' => [

        'label' => '強制削除',

        'modal' => [

            'heading' => ':label 強制削除',

            'actions' => [

                'delete' => [
                    'label' => '削除',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '削除しました',
            ],

        ],

    ],

    'multiple' => [

        'label' => '選択中を強制削除',

        'modal' => [

            'heading' => '選択中の:labelを強制削除',

            'actions' => [

                'delete' => [
                    'label' => '削除',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '削除しました',
            ],

            'deleted_partial' => [
                'title' => ':total件中:count件を削除しました',
                'missing_authorization_failure_message' => ':count件を削除する権限がありません。',
                'missing_processing_failure_message' => ':count件を削除できませんでした。',
            ],

            'deleted_none' => [
                'title' => '削除に失敗しました',
                'missing_authorization_failure_message' => ':count件を削除する権限がありません。',
                'missing_processing_failure_message' => ':count件を削除できませんでした。',
            ],

        ],

    ],

];
