<?php

return [

    'single' => [

        'label' => '关联',

        'modal' => [

            'heading' => '关联 :label',

            'fields' => [

                'record_id' => [
                    'label' => '记录',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => '关联',
                ],

                'associate_another' => [
                    'label' => '关联并关联另一个',
                ],

            ],

        ],

        'notifications' => [

            'associated' => [
                'title' => '已关联',
            ],

        ],

    ],

];
