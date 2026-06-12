<?php

return [

    'title' => 'รีเซ็ตรหัสผ่าน',

    'heading' => 'ลืมรหัสผ่านไหม',

    'actions' => [

        'login' => [
            'label' => 'กลับไปยังหน้าเข้าสู่ระบบ',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ที่อยู่อีเมล',
        ],

        'actions' => [

            'request' => [
                'label' => 'ส่งอีเมล',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'จำนวนการขอได้ถึงขีดจำกัดแล้ว',
            'body' => 'กรุณาลองใหม่อีก :seconds วินาที',
        ],

    ],

];
