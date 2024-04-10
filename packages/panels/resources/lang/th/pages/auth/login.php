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

];
