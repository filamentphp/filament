<?php

return [
    'label' => 'ตั้งค่าแอปยืนยันตัวตน',
    'modal' => [
        'heading' => 'ตั้งค่าแอปยืนยันตัวตน',
        'description' => 'สแกนคิวอาร์โค้ดด้านล่างด้วยแอปยืนยันตัวตนของคุณ (เช่น Google Authenticator) เพื่อเริ่มใช้งาน',
        'form' => [
            'password' => [
                'label' => 'รหัสผ่านปัจจุบัน',
                'validation_attribute' => 'รหัสผ่านปัจจุบัน',
            ],
            'code' => [
                'label' => 'รหัสยืนยัน',
            ],
        ],
        'actions' => [
            'set_up' => [
                'label' => 'ยืนยัน',
            ],
        ],
    ],
    'notifications' => [
        'set_up' => [
            'title' => 'ตั้งค่าแอปยืนยันตัวตนเรียบร้อยแล้ว',
        ],
    ],
];
