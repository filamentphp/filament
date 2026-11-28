<?php

return [

    'label' => 'ส่งออก :label',

    'modal' => [

        'heading' => 'ส่งออก :label',

        'form' => [

            'columns' => [

                'label' => 'คอลัมน์',

                'actions' => [

                    'select_all' => [
                        'label' => 'เลือกทั้งหมด',
                    ],

                    'deselect_all' => [
                        'label' => 'ยกเลิกการเลือกทั้งหมด',
                    ],

                ],

                'form' => [

                    'is_enabled' => [
                        'label' => 'เปิดใช้งานคอลัมน์ :column',
                    ],

                    'label' => [
                        'label' => 'ชื่อคอลัมน์ :column',
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

            'title' => 'ส่งออกข้อมูลเรียบร้อยแล้ว',

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
            'title' => 'ข้อมูลมีขนาดใหญ่เกินไป',
            'body' => 'คุณไม่สามารถส่งออกข้อมูลมากกว่า :count แถวในครั้งเดียว',
        ],

        'no_columns' => [
            'title' => 'ไม่ได้เลือกคอลัมน์',
            'body' => 'กรุณาเลือกอย่างน้อยหนึ่งคอลัมน์เพื่อส่งออก',
        ],

        'started' => [
            'title' => 'เริ่มกระบวนการส่งออกแล้ว',
            'body' => 'เริ่มส่งออกข้อมูลแล้ว :count แถวจะถูกประมวลผลในพื้นหลัง คุณจะได้รับการแจ้งเตือนพร้อมลิงก์ดาวน์โหลดเมื่อเสร็จสิ้น',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
