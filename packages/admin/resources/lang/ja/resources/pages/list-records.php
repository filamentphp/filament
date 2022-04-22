<?php

return [

    'breadcrumb' => '一覧',

    'actions' => [

        'create' => [

            'label' => ':label作成',

            'modal' => [

                'heading' => ':label作成',

                'actions' => [

                    'create' => [
                        'label' => '作成',
                    ],

                    'create_and_create_another' => [
                        'label' => '保存して、続けて作成',
                    ],

                ],

            ],

            'messages' => [
                'created' => '作成しました',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => '削除',

                'messages' => [
                    'deleted' => '削除しました',
                ],

            ],

            'edit' => [

                'label' => '編集',

                'modal' => [

                    'heading' => ':label編集',

                    'actions' => [

                        'save' => [
                            'label' => '保存',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => '保存しました',
                ],

            ],

            'view' => [
                'label' => '詳細',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => '選択の削除',

                'messages' => [
                    'deleted' => '削除しました',
                ],

            ],

        ],

    ],

];
