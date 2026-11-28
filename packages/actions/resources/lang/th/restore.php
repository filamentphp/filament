<?php

return [

    'single' => [

        'label' => 'คืนค่า',

        'modal' => [

            'heading' => 'คืนค่า :label',

            'actions' => [

                'restore' => [
                    'label' => 'คืนค่า',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'คืนค่าข้อมูลเรียบร้อย',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'คืนค่าที่เลือก',

        'modal' => [

            'heading' => 'คืนค่า :label ที่เลือก',

            'actions' => [

                'restore' => [
                    'label' => 'คืนค่า',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'คืนค่าข้อมูลเรียบร้อย',
            ],

            'restored_partial' => [
                'title' => 'คืนค่าแล้ว :count จาก :total รายการ',
                'missing_authorization_failure_message' => 'คุณไม่มีสิทธิ์คืนค่า :count รายการ',
                'missing_processing_failure_message' => 'ไม่สามารถคืนค่าได้ :count รายการ',
            ],

            'restored_none' => [
                'title' => 'คืนค่าไม่สำเร็จ',
                'missing_authorization_failure_message' => 'คุณไม่มีสิทธิ์คืนค่า :count รายการ',
                'missing_processing_failure_message' => 'ไม่สามารถคืนค่าได้ :count รายการ',
            ],

        ],

    ],

];
