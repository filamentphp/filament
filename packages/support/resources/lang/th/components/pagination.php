<?php

return [

    'label' => 'การนำทางการแบ่งหน้า',

    'overview' => '{1} แสดง 1 รายการ|[2,*] แสดง :first ถึง :last จาก :total รายการ',

    'fields' => [

        'records_per_page' => [

            'label' => 'รายการต่อหน้า',

            'options' => [
                'all' => 'ทั้งหมด',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'หน้าแรก',
        ],

        'go_to_page' => [
            'label' => 'ไปหน้า :page',
        ],

        'last' => [
            'label' => 'หน้าสุดท้าย',
        ],

        'next' => [
            'label' => 'ถัดไป',
        ],

        'previous' => [
            'label' => 'หน้าก่อน',
        ],

    ],

];
