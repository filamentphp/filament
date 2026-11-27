<?php

return [

    'column_manager' => [

        'heading' => 'คอลัมน์',

        'position' => 'ตำแหน่งที่ :position จาก :count',

        'position_in_group' => 'ตำแหน่งที่ :position จาก :count ใน :group',

        'reordered' => 'ย้าย :column ไปยังตำแหน่งที่ :position จาก :count แล้ว',

        'reordered_deferred' => 'ย้าย :column ไปยังตำแหน่งที่ :position จาก :count แล้ว กด "ใช้งาน" เพื่อบันทึกการเปลี่ยนแปลง',

        'reordered_in_group' => 'ย้าย :column ไปยังตำแหน่งที่ :position จาก :count ใน :group แล้ว',

        'reordered_in_group_deferred' => 'ย้าย :column ไปยังตำแหน่งที่ :position จาก :count ใน :group แล้ว กด "ใช้งาน" เพื่อบันทึกการเปลี่ยนแปลง',

        'actions' => [

            'apply' => [
                'label' => 'ใช้งาน',
            ],

            'move_up' => [
                'label' => 'เลื่อน :column ขึ้น',
            ],

            'move_down' => [
                'label' => 'เลื่อน :column ลง',
            ],

            'reorder' => [
                'label' => 'จัดลำดับคอลัมน์',
            ],

            'reset' => [
                'label' => 'รีเซ็ต',
            ],

        ],

    ],

    'columns' => [

        'actions' => [
            'label' => 'การดำเนินการ',
        ],

        'editable' => [
            'saving' => 'กำลังบันทึกการเปลี่ยนแปลง...',
            'saved' => 'บันทึกการเปลี่ยนแปลงแล้ว',
            'failure' => 'ไม่สามารถยืนยันการบันทึกได้ กรุณารีเฟรชหน้าก่อนลองอีกครั้ง',
            'unavailable' => 'รายการนี้ไม่สามารถแก้ไขได้แล้ว',
        ],

        'icon' => [

            'boolean' => [
                'true' => 'ใช่',
                'false' => 'ไม่ใช่',
            ],

        ],

        'select' => [

            'loading_message' => 'กำลังโหลด...',

            'no_options_message' => 'ไม่มีตัวเลือกที่ใช้งานได้',

            'no_search_results_message' => 'ไม่มีตัวเลือกที่ตรงกับการค้นหา',

            'placeholder' => 'เลือกตัวเลือก',

            'searching_message' => 'กำลังค้นหา...',

            'search_prompt' => 'เริ่มพิมพ์เพื่อค้นหา',

        ],

        'text' => [

            'actions' => [
                'collapse_list' => 'แสดงน้อยกว่านี้ :count รายการ',
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

        'reorder_record' => [
            'label' => 'จัดลำดับรายการ :key',
        ],

        'move_record_up' => [
            'label' => 'เลื่อน :label ขึ้น',
        ],

        'move_record_down' => [
            'label' => 'เลื่อน :label ลง',
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

        'column_manager' => [
            'label' => 'สลับคอลัมน์',
        ],

        'toggle_record_content' => [
            'label' => 'ขยาย/ยุบรายการ :key',
        ],

    ],

    'empty' => [

        'heading' => 'ไม่พบข้อมูล :model',

        'description' => 'เริ่มสร้าง :model เพื่อเริ่มต้นใช้งาน',

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

            'relationship' => [
                'empty_option_label' => 'ไม่มี',
            ],

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

    'loading' => 'กำลังโหลด...',

    'reorder_indicator' => 'ลากรายการและวางในลำดับ',

    'reordering' => [
        'position' => ':label ตำแหน่งที่ :position จาก :count',
        'moved' => 'ย้าย :label ไปยังตำแหน่งที่ :position จาก :count แล้ว',
    ],

    'result_count' => '{0} ไม่มีผลลัพธ์|[1,*] :count รายการ',

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

    'default_model_label' => 'รายการ',

];
