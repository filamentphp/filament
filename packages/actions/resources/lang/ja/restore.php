<?php

return [

    'single' => [

        'label' => '復元',

        'modal' => [

            'heading' => ':labelを復元',

            'actions' => [

                'restore' => [
                    'label' => '復元',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => '復元しました',
            ],

        ],

    ],

    'multiple' => [

        'label' => '選択中を復元',

        'modal' => [

            'heading' => '選択中の::labelを復元',

            'actions' => [

                'restore' => [
                    'label' => '復元',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => '復旧しました',
            ],

            'restored_partial' => [
                'title' => ':total件中:count件を復元しました',
                'missing_authorization_failure_message' => ':count件を復元する権限がありません。',
                'missing_processing_failure_message' => ':count件を復元できませんでした。',
            ],

            'restored_none' => [
                'title' => '復元に失敗しました',
                'missing_authorization_failure_message' => ':count件を復元する権限がありません。',
                'missing_processing_failure_message' => ':count件を復元できませんでした。',
            ],

        ],

    ],

];
