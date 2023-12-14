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

        ],

    ],

];
