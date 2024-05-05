<?php

return [

    'single' => [

        'label' => 'บังคับลบ',

        'modal' => [

            'heading' => 'บังคับลบ :label',

            'actions' => [

                'delete' => [
                    'label' => 'ลบ',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'ลบข้อมูลเรียบร้อย',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'บังคับลบที่เลือก',

        'modal' => [

            'heading' => 'บังคับลบ :label ที่เลือก',

            'actions' => [

                'delete' => [
                    'label' => 'ลบ',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'ลบข้อมูลเรียบร้อย',
            ],

        ],

    ],

];
