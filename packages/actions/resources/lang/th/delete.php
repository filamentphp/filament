<?php

return [

    'single' => [

        'label' => 'ลบ',

        'modal' => [

            'heading' => 'ลบ :label',

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

        'label' => 'ลบที่เลือก',

        'modal' => [

            'heading' => 'ลบ :label ที่เลือก',

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

            'deleted_partial' => [
                'title' => 'ลบแล้ว :count จาก :total รายการ',
                'missing_authorization_failure_message' => 'คุณไม่มีสิทธิ์ลบ :count รายการ',
                'missing_processing_failure_message' => 'ไม่สามารถลบได้ :count รายการ',
            ],

            'deleted_none' => [
                'title' => 'ลบไม่สำเร็จ',
                'missing_authorization_failure_message' => 'คุณไม่มีสิทธิ์ลบ :count รายการ',
                'missing_processing_failure_message' => 'ไม่สามารถลบได้ :count รายการ',
            ],

        ],

    ],

];
