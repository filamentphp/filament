<?php

return [

    'column_toggle' => [

        'heading' => 'คอลัมน์',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'แสดงให้น้อยกว่านี้ :count รายการ',
                'expand_list' => 'แสดงอีก :count รายการ',
            ],

            'more_list_items' => 'และอีก :count รายการ',

        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'เลือก/ไม่เลือกรายการทั้งหมดสำหรับการดำเนินการเป็นกลุ่ม',
        ],

        'bulk_select_record' => [
            'label' => 'เลือก/ไม่เลือกรายการ :key สำหรับการดำเนินการเป็นกลุ่ม',
        ],

        'bulk_select_group' => [
            'label' => 'เลือก/ไม่เลือกกลุ่ม :title สำหรับการดำเนินการเป็นกลุ่ม',
        ],

        'search' => [
            'label' => 'ค้นหา',
            'placeholder' => 'ค้นหา',
            'indicator' => 'ค้นหา',
        ],

    ],

    'summary' => [

        'heading' => 'สรุป',

        'subheadings' => [
            'all' => ':label ทุกรายการ',
            'group' => 'สรุป :group',
            'page' => 'หน้านี้',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'เฉลี่ย',
            ],

            'count' => [
                'label' => 'จำนวน',
            ],

            'sum' => [
                'label' => 'รวม',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'เลิกการจัดลำดับรายการ',
        ],

        'enable_reordering' => [
            'label' => 'จัดลำดับรายการ',
        ],

        'filter' => [
            'label' => 'ตัวกรอง',
        ],

        'group' => [
            'label' => 'จัดกลุ่ม',
        ],

        'open_bulk_actions' => [
            'label' => 'การดำเนินการเป็นกลุ่ม',
        ],

        'toggle_columns' => [
            'label' => 'สลับคอลัมน์',
        ],

    ],

    'empty' => [

        'heading' => 'ไม่มี :model',

        'description' => 'สร้าง :model เพื่อเริ่มต้น',

    ],

    'filters' => [

        'actions' => [

            'apply' => [
                'label' => 'ใช้ตัวกรอง',
            ],

            'remove' => [
                'label' => 'ลบตัวกรอง',
            ],

            'remove_all' => [
                'label' => 'ลบตัวกรองทั้งหมด',
                'tooltip' => 'ลบตัวกรองทั้งหมด',
            ],

            'reset' => [
                'label' => 'รีเซ็ต',
            ],

        ],

        'heading' => 'ตัวกรอง',

        'indicator' => 'ตัวกรองที่ใช้งานอยู่',

        'multi_select' => [
            'placeholder' => 'ทั้งหมด',
        ],

        'select' => [
            'placeholder' => 'ทั้งหมด',
        ],

        'trashed' => [

            'label' => 'รายการที่ถูกลบ',

            'only_trashed' => 'รายการที่ถูกลบเท่านั้น',

            'with_trashed' => 'พร้อมรายการที่ถูกลบ',

            'without_trashed' => 'โดยไม่รวมรายการที่ถูกลบ',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'จัดกลุ่มตาม',
                'placeholder' => 'จัดกลุ่มตาม',
            ],

            'direction' => [

                'label' => 'เรียงลำดับกลุ่ม',

                'options' => [
                    'asc' => 'เรียงจากน้อยไปมาก',
                    'desc' => 'เรียงจากมากไปน้อย',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'ลากรายการและวางในลำดับ',

    'selection_indicator' => [

        'selected_count' => 'เลือก 1 รายการ|เลือก :count รายการ',

        'actions' => [

            'select_all' => [
                'label' => 'เลือกทั้ง :count รายการ',
            ],

            'deselect_all' => [
                'label' => 'ยกเลิกการเลือกทั้งหมด',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'เรียงลำดับโดย',
            ],

            'direction' => [

                'label' => 'ทิศทางการเรียงลำดับ',

                'options' => [
                    'asc' => 'เรียงจากน้อยไปมาก',
                    'desc' => 'เรียงจากมากไปน้อย',
                ],

            ],

        ],

    ],

];
