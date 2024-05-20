<?php

return [

    'single' => [

        'label' => 'เชื่อมโยง',

        'modal' => [

            'heading' => 'เชื่อมโยง :label',

            'fields' => [

                'record_id' => [
                    'label' => 'รายการ',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => 'เชื่อมโยง',
                ],

                'associate_another' => [
                    'label' => 'เชื่อมโยงและเชื่อมโยงรายการอื่น',
                ],

            ],

        ],

        'notifications' => [

            'associated' => [
                'title' => 'บันทึกเรียบร้อย',
            ],

        ],

    ],

];
