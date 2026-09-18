<?php

return [
    'label' => 'สร้างรหัสกู้คืนใหม่',
    'modal' => [
        'heading' => 'สร้างรหัสกู้คืนใหม่',
        'description' => 'เมื่อคุณสร้างรหัสกู้คืนใหม่ รหัสกู้คืนเดิมจะใช้งานไม่ได้อีกต่อไป',
        'form' => [
            'password' => [
                'label' => 'กรอกรหัสผ่านปัจจุบันของคุณ',
                'validation_attribute' => 'รหัสผ่าน',
            ],
        ],
        'actions' => [
            'regenerate' => [
                'label' => 'สร้างใหม่',
            ],
        ],
    ],
    'notifications' => [
        'regenerated' => [
            'title' => 'สร้างรหัสกู้คืนใหม่เรียบร้อยแล้ว',
        ],
    ],
];
