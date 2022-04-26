<?php

return [

    'title' => ':label編集',

    'breadcrumb' => '編集',

    'actions' => [

        'delete' => [

            'label' => '削除',

            'modal' => [

                'heading' => ':label削除',

                'subheading' => '本当に実行しますか？',

                'buttons' => [

                    'delete' => [
                        'label' => '削除',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => '削除しました',
            ],

        ],

        'view' => [
            'label' => '詳細',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'キャンセル',
            ],

            'save' => [
                'label' => '保存',
            ],

        ],

    ],

    'messages' => [
        'saved' => '保存しました',
    ],

];
