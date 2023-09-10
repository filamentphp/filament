<?php

return [

    'single' => [

        'label' => '紐づける',

        'modal' => [

            'heading' => ':labelを紐づける',

            'fields' => [

                'record_id' => [
                    'label' => 'レコード',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => '紐づける',
                ],

                'associate_another' => [
                    'label' => '保存して、続けて紐づける',
                ],

            ],

        ],

        'notifications' => [

            'associated' => [
                'title' => '紐づけました',
            ],

        ],

    ],

];
