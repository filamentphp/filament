<?php

return [

    'label' => 'นำเข้า',

    'modal' => [

        'heading' => 'นำเข้า :label',

        'form' => [

            'file' => [
                'label' => 'ไฟล์',
                'placeholder' => 'อัปโหลดไฟล์ CSV',
            ],

            'columns' => [
                'label' => 'จับคู่คอลัมน์',
            ],

        ],

        'actions' => [

            'import' => [
                'label' => 'นำเข้า',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [
            'title' => 'นำเข้าข้อมูลเรียบร้อยแล้ว',
            'body' => 'นำเข้าข้อมูลสำเร็จ :count แถว',
        ],

        'max_rows' => [
            'title' => 'ไฟล์มีขนาดใหญ่เกินไป',
            'body' => 'คุณไม่สามารถนำเข้าข้อมูลมากกว่า :count แถวในครั้งเดียว',
        ],

        'started' => [
            'title' => 'เริ่มกระบวนการนำเข้าแล้ว',
            'body' => 'ข้อมูลจะถูกนำเข้าในพื้นหลัง และคุณจะได้รับการแจ้งเตือนเมื่อเสร็จสิ้น',
        ],

    ],

    'example_csv_file_name' => 'import-:resource-example',

];
