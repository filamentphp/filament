<?php

return [

    'single' => [

        'label' => '完全に削除',

        'modal' => [

            'heading' => ':labelを完全に削除',

            'actions' => [

                'delete' => [
                    'label' => '完全に削除',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '完全に削除しました',
            ],

        ],

    ],

    'multiple' => [

        'label' => '選択中を完全に削除',

        'modal' => [

            'heading' => '選択中の:labelを完全に削除',

            'actions' => [

                'delete' => [
                    'label' => '完全に削除',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => '完全に削除しました',
            ],

            'deleted_partial' => [
                'title' => ':total件中:count件を完全に削除しました',
                'missing_authorization_failure_message' => ':count件を完全に削除する権限がありません。',
                'missing_processing_failure_message' => ':count件を完全に削除できませんでした。',
            ],

            'deleted_none' => [
                'title' => '完全な削除に失敗しました',
                'missing_authorization_failure_message' => ':count件を完全な削除する権限がありません。',
                'missing_processing_failure_message' => ':count件を完全な削除できませんでした。',
            ],

        ],

    ],

];
