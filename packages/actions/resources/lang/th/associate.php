<?php

return [

    'single' => [

        'label' => 'เชื่อมโยง',

        'modal' => [

            'heading' => 'เชื่อมโยง :label',

            'fields' => [

                'record_id' => [
                    'label' => 'ระเบียน',
                ],

            ],

            'actions' => [

                'associate' => [
                    'label' => 'เชื่อมโยง',
                ],

                'associate_another' => [
                    'label' => 'เชื่อมโยงและเชื่อมโยงระเบียนอื่น',
                ],

            ],

        ],

        'notifications' => [

            'associated' => [
                'title' => 'เชื่อมโยงแล้ว',
            ],

        ],

    ],

];
