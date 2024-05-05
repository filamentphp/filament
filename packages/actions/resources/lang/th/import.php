<?php

return [

    'label' => 'นำเข้า :label',

    'modal' => [

        'heading' => 'นำเข้า :label',

        'form' => [

            'file' => [
                'label' => 'ไฟล์',
                'placeholder' => 'อัปโหลดไฟล์ CSV',
            ],

            'columns' => [
                'label' => 'คอลัมน์',
                'placeholder' => 'เลือกคอลัมน์',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'ดาวน์โหลดตัวอย่างไฟล์ CSV',
            ],

            'import' => [
                'label' => 'นำเข้า',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'การนำเข้าเสร็จสิ้น',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'ดาวน์โหลดข้อมูลเกี่ยวกับแถวที่ไม่สำเร็จ|ดาวน์โหลดข้อมูลเกี่ยวกับแถวที่ไม่สำเร็จ',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'ไฟล์ CSV ที่อัปโหลดใหญ่เกินไป',
            'body' => 'ไม่สามารถนำเข้ามากกว่า 1 แถวในครั้งเดียวได้|ไม่สามารถนำเข้ามากกว่า :count แถวในครั้งเดียวได้',
        ],

        'started' => [
            'title' => 'เริ่มต้นการนำเข้าข้อมูล',
            'body' => 'การนำเข้าได้เริ่มต้นแล้ว และ 1 รายการจะถูกประมวลผลในเบื้องหลัง|การนำเข้าได้เริ่มต้นแล้ว และ :count รายการจะถูกประมวลผลในเบื้องหลัง',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'error',
        'system_error' => 'ข้อผิดพลาดในระบบ โปรดติดต่อฝ่ายสนับสนุน',
    ],

];
