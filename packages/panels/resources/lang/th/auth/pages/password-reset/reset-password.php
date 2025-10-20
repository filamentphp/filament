<?php

return [

    'title' => 'รีเซ็ตรหัสผ่าน',

    'heading' => 'รีเซ็ตรหัสผ่าน',

    'form' => [

        'email' => [
            'label' => 'ที่อยู่อีเมล',
        ],

        'password' => [
            'label' => 'รหัสผ่าน',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'ยืนยันรหัสผ่าน',
        ],

        'actions' => [

            'reset' => [
                'label' => 'รีเซ็ตรหัสผ่าน',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'จำนวนครั้งในการพยายามรีเซ็ตรหัสผ่านได้ถึงขีดจำกัดแล้ว',
            'body' => 'กรุณาลองใหม่อีก :seconds วินาที',
        ],

    ],

];
