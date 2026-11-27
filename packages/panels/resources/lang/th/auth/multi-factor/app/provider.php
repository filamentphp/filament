<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'แอปยืนยันตัวตน',

            'below_content' => 'ใช้แอปที่ปลอดภัยเพื่อสร้างรหัสชั่วคราวสำหรับยืนยันการเข้าสู่ระบบ',

            'messages' => [
                'enabled' => 'เปิดใช้งาน',
                'disabled' => 'ปิดการใช้งาน',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'ใช้รหัสจากแอปยืนยันตัวตน',

        'code' => [

            'label' => 'กรอกรหัส 6 หลักจากแอปยืนยันตัวตน',

            'validation_attribute' => 'รหัสยืนยัน',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'ใช้รหัสกู้คืนแทน',
                ],

            ],

            'messages' => [

                'invalid' => 'รหัสที่คุณกรอกไม่ถูกต้อง',

            ],

        ],

        'recovery_code' => [

            'label' => 'หรือกรอกรหัสกู้คืน',

            'validation_attribute' => 'รหัสกู้คืน',

            'messages' => [

                'invalid' => 'รหัสกู้คืนที่คุณกรอกไม่ถูกต้อง',

            ],

        ],

    ],

];
