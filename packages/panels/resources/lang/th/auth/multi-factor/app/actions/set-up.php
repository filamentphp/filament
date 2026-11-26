<?php

return [

    'label' => 'ตั้งค่า',

    'modal' => [

        'heading' => 'ตั้งค่าแอปยืนยันตัวตน',

        'description' => <<<'BLADE'
            คุณจะต้องใช้แอปอย่าง Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) เพื่อดำเนินการนี้ให้เสร็จสิ้น
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => 'สแกนคิวอาร์โค้ดนี้ด้วยแอปยืนยันตัวตนของคุณ:',

                'alt' => 'คิวอาร์โค้ดสำหรับสแกนด้วยแอปยืนยันตัวตน',

            ],

            'text_code' => [

                'instruction' => 'หรือกรอกรหัสนี้ด้วยตนเอง:',

                'messages' => [
                    'copied' => 'คัดลอกแล้ว',
                ],

            ],

            'recovery_codes' => [

                'instruction' => 'กรุณาเก็บรหัสกู้คืนต่อไปนี้ไว้ในที่ปลอดภัย รหัสจะแสดงเพียงครั้งเดียว แต่คุณจะต้องใช้รหัสนี้หากไม่สามารถเข้าถึงแอปยืนยันตัวตนได้:',

            ],

        ],

        'form' => [

            'password' => [
                'label' => 'รหัสผ่านปัจจุบัน',
                'validation_attribute' => 'รหัสผ่านปัจจุบัน',
            ],

            'code' => [

                'label' => 'กรอกรหัส 6 หลักจากแอปยืนยันตัวตน',

                'validation_attribute' => 'รหัสยืนยัน',

                'below_content' => 'คุณจะต้องกรอกรหัส 6 หลักจากแอปยืนยันตัวตนทุกครั้งที่เข้าสู่ระบบหรือทำรายการที่สำคัญ',

                'messages' => [

                    'invalid' => 'รหัสที่คุณกรอกไม่ถูกต้อง',

                    'rate_limited' => 'พยายามหลายครั้งเกินไป กรุณาลองใหม่ภายหลัง',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'เปิดใช้งานแอปยืนยันตัวตน',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'เปิดใช้งานแอปยืนยันตัวตนแล้ว',
        ],

    ],

];
