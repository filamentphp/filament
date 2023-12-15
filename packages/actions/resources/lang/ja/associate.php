<?php

return [

    'single' => [

        'label' => '関連付ける',

        'modal' => [

            'heading' => ':labelを関連付ける',

            'fields' => [

                'record_id' => [
                    'label' => 'レコード',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => '関連付ける',
                ],

                'associate_another' => [
                    'label' => '保存して、続けて関連付ける',
                ],

            ],

        ],

        'notifications' => [

            'associated' => [
                'title' => '関連付けしました',
            ],

        ],

    ],

];
