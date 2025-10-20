<?php

return [

    'single' => [

        'label' => '關聯',

        'modal' => [

            'heading' => '關聯 :label',

            'fields' => [

                'record_id' => [
                    'label' => '資料',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => '關聯',
                ],

                'associate_another' => [
                    'label' => '關聯後繼續關聯另一個',
                ],

            ],

        ],

        'notifications' => [

            'associated' => [
                'title' => '已關聯',
            ],

        ],

    ],

];
