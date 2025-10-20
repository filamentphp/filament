<?php

return [

    'title' => 'ลงทะเบียน',

    'heading' => 'ลงทะเบียน',

    'actions' => [

        'login' => [
            'before' => 'หรือ',
            'label' => 'เข้าสู่ระบบบัญชีของคุณ',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ที่อยู่อีเมล',
        ],

        'name' => [
            'label' => 'ชื่อ',
        ],

        'password' => [
            'label' => 'รหัสผ่าน',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'ยืนยันรหัสผ่าน',
        ],

        'actions' => [

            'register' => [
                'label' => 'ลงทะเบียน',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'จำนวนครั้งในการพยายามลงทะเบียนได้ถึงขีดจำกัดแล้ว',
            'body' => 'กรุณาลองใหม่อีก :seconds วินาที',
        ],

    ],

];
