<?php

return [

    'label' => 'ส่ง :label ออก',

    'modal' => [

        'heading' => 'ส่ง :label ออก',

        'form' => [

            'columns' => [

                'label' => 'คอลัมน์',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column เปิดใช้งาน',
                    ],

                    'label' => [
                        'label' => 'ป้ายชื่อ :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'ส่งออก',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'การส่งออกเสร็จสิ้น',

            'actions' => [

                'download_csv' => [
                    'label' => 'ดาวน์โหลด .csv',
                ],

                'download_xlsx' => [
                    'label' => 'ดาวน์โหลด .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'ข้อมูลส่งออกใหญ่เกินไป',
            'body' => 'ไม่สามารถส่งออกได้มากกว่า 1 แถวในครั้งเดียว|ไม่สามารถส่งออกได้มากกว่า :count แถวในครั้งเดียว',
        ],

        'started' => [
            'title' => 'การส่งออกเริ่มต้นแล้ว',
            'body' => 'การส่งออกได้เริ่มต้นแล้ว และ 1 แถวจะถูกประมวลผลในเบื้องหลัง|การส่งออกได้เริ่มต้นแล้ว และ :count แถวจะถูกประมวลผลในเบื้องหลัง',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
