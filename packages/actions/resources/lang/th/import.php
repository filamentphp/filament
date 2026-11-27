<?php

return [

    'label' => 'นำเข้า :label',

    'modal' => [

        'heading' => 'นำเข้า :label',

        'form' => [

            'file' => [

                'label' => 'ไฟล์',

                'placeholder' => 'อัปโหลดไฟล์ CSV',

                'rules' => [
                    'duplicate_columns' => '{0} ไฟล์ต้องไม่มีหัวคอลัมน์ที่ว่างเกินหนึ่งคอลัมน์|{1,*} ไฟล์ต้องไม่มีหัวคอลัมน์ซ้ำกัน: :columns',
                ],

            ],

            'columns' => [
                'label' => 'จับคู่คอลัมน์',
                'placeholder' => 'เลือกคอลัมน์',
            ],

        ],

        'actions' => [

            'download_example' => [
                'label' => 'ดาวน์โหลดไฟล์ CSV ตัวอย่าง',
            ],

            'import' => [
                'label' => 'นำเข้า',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'นำเข้าข้อมูลเรียบร้อยแล้ว',

            'actions' => [

                'download_failed_rows_csv' => [
                    'label' => 'ดาวน์โหลดข้อมูลแถวที่นำเข้าไม่สำเร็จ',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'ไฟล์มีขนาดใหญ่เกินไป',
            'body' => 'คุณไม่สามารถนำเข้าข้อมูลมากกว่า :count แถวในครั้งเดียว',
        ],

        'started' => [
            'title' => 'เริ่มกระบวนการนำเข้าแล้ว',
            'body' => 'เริ่มนำเข้าข้อมูลแล้ว :count แถวจะถูกประมวลผลในพื้นหลัง',
        ],

    ],

    'example_csv' => [
        'file_name' => ':importer-example',
    ],

    'failure_csv' => [
        'file_name' => 'import-:import_id-:csv_name-failed-rows',
        'error_header' => 'ข้อผิดพลาด',
        'system_error' => 'เกิดข้อผิดพลาดของระบบ กรุณาติดต่อฝ่ายสนับสนุน',
        'column_mapping_required_for_new_record' => 'คอลัมน์ :attribute ยังไม่ได้จับคู่กับคอลัมน์ในไฟล์ แต่จำเป็นต้องใช้ในการสร้างข้อมูลใหม่',
    ],

];
