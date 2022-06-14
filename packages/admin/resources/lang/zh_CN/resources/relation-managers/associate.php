<?php

return [

    'action' => [

        'label' => '关联',

        'modal' => [

            'heading' => '关联 :label',

            'fields' => [

                'record_ids' => [
                    'label' => '记录',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => '关联',
                ],

                'associate_and_associate_another' => [
                    'label' => '关联并关联另一个',
                ],

            ],

        ],

        'messages' => [
            'associated' => '已关联',
        ],

    ],

];
