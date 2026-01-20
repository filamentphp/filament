<?php

return [

    'label' => 'ส่งออก',

    'modal' => [

        'heading' => 'ส่งออก :label',

        'form' => [

            'columns' => [
                'label' => 'คอลัมน์',
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
                'download_csv' => 'ดาวน์โหลด .csv',
                'download_xlsx' => 'ดาวน์โหลด .xlsx',
            ],
        ],

        'max_rows' => [
            'title' => 'ข้อมูลมีขนาดใหญ่เกินไป',
            'body' => 'คุณไม่สามารถส่งออกข้อมูลมากกว่า :count แถวในครั้งเดียว',
        ],

        'started' => [
            'title' => 'เริ่มกระบวนการส่งออกแล้ว',
            'body' => 'ข้อมูลจะถูกส่งออกในพื้นหลัง และคุณจะได้รับการแจ้งเตือนเมื่อเสร็จสิ้น',
        ],

    ],

    'file_name' => 'export-:resource-:date',

];
