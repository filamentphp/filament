<?php

return [

    'title' => 'เข้าสู่ระบบ',

    'heading' => 'เข้าสู่ระบบ',

    'actions' => [

        'register' => [
            'before' => 'หรือ',
            'label' => 'สมัครบัญชี',
        ],

        'request_password_reset' => [
            'label' => 'ลืมรหัสผ่านไหม',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ที่อยู่อีเมล',
        ],

        'password' => [
            'label' => 'รหัสผ่าน',
        ],

        'remember' => [
            'label' => 'จดจำฉัน',
        ],

        'code' => [
            'label' => 'รหัสยืนยัน',
        ],

        'recovery_code' => [
            'label' => 'รหัสกู้คืน',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'เข้าสู่ระบบ',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'ข้อมูลนี้ไม่ตรงกับบันทึกในระบบ',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'จำนวนครั้งในการพยายามเข้าสู่ระบบได้ถึงขีดจำกัดแล้ว',
            'body' => 'กรุณาลองใหม่อีก :seconds วินาที',
        ],

    ],

    'multi_factor' => [

        'heading' => 'การยืนยันตัวตนหลายขั้นตอน',

        'description' => 'กรุณายืนยันการเข้าสู่ระบบของคุณโดยใช้วิธีใดวิธีหนึ่งด้านล่าง',

        'actions' => [

            'use_recovery_code' => [
                'label' => 'ใช้รหัสกู้คืน',
            ],

            'use_authentication_code' => [
                'label' => 'ใช้รหัสยืนยันตัวตน',
            ],

        ],

    ],

];
