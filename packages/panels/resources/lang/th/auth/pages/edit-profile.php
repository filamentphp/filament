<?php

return [

    'label' => 'โปรไฟล์',

    'form' => [

        'email' => [
            'label' => 'ที่อยู่อีเมล',
        ],

        'name' => [
            'label' => 'ชื่อ',
        ],

        'password' => [
            'label' => 'รหัสผ่านใหม่',
            'validation_attribute' => 'รหัสผ่าน',
        ],

        'password_confirmation' => [
            'label' => 'ยืนยันรหัสผ่านใหม่',
            'validation_attribute' => 'การยืนยันรหัสผ่าน',
        ],

        'current_password' => [
            'label' => 'รหัสผ่านปัจจุบัน',
            'below_content' => 'เพื่อความปลอดภัย กรุณายืนยันรหัสผ่านเพื่อดำเนินการต่อ',
            'validation_attribute' => 'รหัสผ่านปัจจุบัน',
        ],

        'actions' => [

            'save' => [
                'label' => 'บันทึก',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'การยืนยันตัวตนสองขั้นตอน (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'ส่งคำขอเปลี่ยนอีเมลแล้ว',
            'body' => 'เราได้ส่งคำขอเปลี่ยนอีเมลไปที่ :email แล้ว กรุณาตรวจสอบอีเมลเพื่อยืนยันการเปลี่ยนแปลง',
        ],

        'saved' => [
            'title' => 'บันทึกข้อมูลเรียบร้อย',
        ],

        'throttled' => [
            'title' => 'มีการส่งคำขอมากเกินไป กรุณาลองใหม่อีก :seconds วินาที',
            'body' => 'กรุณาลองใหม่อีก :seconds วินาที',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'ยกเลิก',
        ],

    ],

];
