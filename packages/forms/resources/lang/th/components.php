<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'ทำสำเนา',
            ],

            'add' => [

                'label' => 'เพิ่มไปยัง:label',

                'modal' => [

                    'heading' => 'เพิ่มไปยัง:label',

                    'actions' => [

                        'add' => [
                            'label' => 'เพิ่ม',
                        ],

                    ],

                ],

            ],

            'add_between' => [
                'label' => 'แทรกระหว่างบล็อก',
            ],

            'delete' => [
                'label' => 'ลบ',
            ],

            'edit' => [

                'label' => 'แก้ไข',

                'modal' => [

                    'heading' => 'แก้ไขบล็อค',

                    'actions' => [

                        'save' => [
                            'label' => 'บันทึก',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'ย้าย',
            ],

            'move_down' => [
                'label' => 'เลื่อนลง',
            ],

            'move_up' => [
                'label' => 'เลื่อนขึ้น',
            ],

            'collapse' => [
                'label' => 'ยุบ',
            ],

            'expand' => [
                'label' => 'ขยาย',
            ],

            'collapse_all' => [
                'label' => 'ยุบทั้งหมด',
            ],

            'expand_all' => [
                'label' => 'ขยายทั้งหมด',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'เลือกทั้งหมด',
            ],

            'select_all' => [
                'label' => 'ยกเลิกการเลือกทั้งหมด',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'ยกเลิก',
                ],

                'drag_crop' => [
                    'label' => 'โหมดลาก "ตัดรูป"',
                ],

                'drag_move' => [
                    'label' => 'โหมดลาก "ย้าย"',
                ],

                'flip_horizontal' => [
                    'label' => 'พลิกภาพแนวนอน',
                ],

                'flip_vertical' => [
                    'label' => 'พลิกภาพแนวตั้ง',
                ],

                'move_down' => [
                    'label' => 'ย้ายรูปลง',
                ],

                'move_left' => [
                    'label' => 'ย้ายรูปไปทางซ้าย',
                ],

                'move_right' => [
                    'label' => 'ย้ายรูปไปทางขวา',
                ],

                'move_up' => [
                    'label' => 'ย้ายรูปขึ้น',
                ],

                'reset' => [
                    'label' => 'รีเซ็ต',
                ],

                'rotate_left' => [
                    'label' => 'หมุนภาพไปทางซ้าย',
                ],

                'rotate_right' => [
                    'label' => 'หมุนภาพไปทางขวา',
                ],

                'set_aspect_ratio' => [
                    'label' => 'ตั้งอัตราส่วนภาพเป็น :ratio',
                ],

                'save' => [
                    'label' => 'บันทึก',
                ],

                'zoom_100' => [
                    'label' => 'ขยายภาพเป็น 100%',
                ],

                'zoom_in' => [
                    'label' => 'ซูมเข้า',
                ],

                'zoom_out' => [
                    'label' => 'ซูมออก',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'ความสูง',
                    'unit' => 'พิก',
                ],

                'rotation' => [
                    'label' => 'หมุน',
                    'unit' => 'องศา',
                ],

                'width' => [
                    'label' => 'กว้าง',
                    'unit' => 'พิก',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => 'พิก',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => 'พิก',
                ],

            ],

            'aspect_ratios' => [

                'label' => 'อัตราส่วนภาพ',

                'no_fixed' => [
                    'label' => 'อิสระ',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'การแก้ไขไฟล์ SVG ไม่แนะนำ เนื่องจากอาจเกิดการสูญเสียคุณภาพเมื่อมีการปรับขนาด \n แน่ใจหรือไม่ว่าต้องการดำเนินการต่อ?',
                    'disabled' => 'การแก้ไขไฟล์ SVG ถูกปิดใช้งาน เนื่องจากอาจทำให้เกิดการสูญเสียคุณภาพเมื่อมีการปรับขนาด',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'เพิ่มแถว',
            ],

            'delete' => [
                'label' => 'ลบแถว',
            ],

            'reorder' => [
                'label' => 'จัดลำดับแถว',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'คีย์',
            ],

            'value' => [
                'label' => 'ค่า',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'แนบไฟล์',
            'blockquote' => 'บล็อกคำพูด',
            'bold' => 'ตัวหนา',
            'bullet_list' => 'รายการสัญลักษณ์แสดงหัวข้อย่อย',
            'code_block' => 'บล็อกโค้ด',
            'heading' => 'หัวข้อ',
            'italic' => 'ตัวเอียง',
            'link' => 'ลิงก์',
            'ordered_list' => 'รายการลําดับเลข',
            'redo' => 'ทำอีกครั้ง',
            'strike' => 'ขีดฆ่า',
            'table' => 'ตาราง',
            'undo' => 'ยกเลิกทำ',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'เลือก',

                'actions' => [

                    'select' => [
                        'label' => 'เลือก',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'ใช่',
            'false' => 'ไม่ใช่',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'เพิ่มไปยัง:label',
            ],

            'add_between' => [
                'label' => 'แทรกระหว่าง',
            ],

            'delete' => [
                'label' => 'ลบ',
            ],

            'clone' => [
                'label' => 'ทำสำเนา',
            ],

            'reorder' => [
                'label' => 'ย้าย',
            ],

            'move_down' => [
                'label' => 'เลื่อนลง',
            ],

            'move_up' => [
                'label' => 'เลื่อนขึ้น',
            ],

            'collapse' => [
                'label' => 'ยุบ',
            ],

            'expand' => [
                'label' => 'ขยาย',
            ],

            'collapse_all' => [
                'label' => 'ยุบทั้งหมด',
            ],

            'expand_all' => [
                'label' => 'ขยายทั้งหมด',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'อัพโหลดไฟล์',

                'modal' => [

                    'heading' => 'อัพโหลดไฟล์',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'ไฟล์',
                                'existing' => 'ทับไฟล์',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'ข้อความ Alt',
                                'existing' => 'แก้ไขข้อความ Alt',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'แทรก',
                        ],

                        'save' => [
                            'label' => 'บันทึก',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'แก้ไข',

                'modal' => [

                    'heading' => 'ลิ้งค์',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'เปิดในแท็บใหม่',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'No merge tag results.',

        'tools' => [
            'align_center' => 'จัดกึ่งกลาง',
            'align_end' => 'จัดชิดขวา',
            'align_justify' => 'จัดชิดขอบทั้งสอง',
            'align_start' => 'จัดชิดซ้าย',
            'attach_files' => 'แนบไฟล์',
            'blockquote' => 'ย่อหน้าพิเศษ',
            'bold' => 'ตัวหนา',
            'bullet_list' => 'รายการแบบจุด',
            'clear_formatting' => 'ล้างรูปแบบ',
            'code' => 'โค้ด',
            'code_block' => 'บล็อกโค้ด',
            'custom_blocks' => 'บล็อก',
            'details' => 'รายละเอียด',
            'h1' => 'หัวข้อหลัก',
            'h2' => 'หัวข้อรอง',
            'h3' => 'หัวข้อย่อย',
            'highlight' => 'เน้นข้อความ',
            'horizontal_rule' => 'เส้นคั่นแนวนอน',
            'italic' => 'ตัวเอียง',
            'lead' => 'ข้อความนำ',
            'link' => 'ลิงก์',
            'merge_tags' => 'รวมแท็ก',
            'ordered_list' => 'รายการแบบตัวเลข',
            'redo' => 'ทำซ้ำ',
            'small' => 'ข้อความขนาดเล็ก',
            'strike' => 'ขีดฆ่า',
            'subscript' => 'ตัวห้อย',
            'superscript' => 'ตัวยก',
            'table' => 'ตาราง',
            'table_delete' => 'ลบตาราง',
            'table_add_column_before' => 'เพิ่มคอลัมน์ด้านซ้าย',
            'table_add_column_after' => 'เพิ่มคอลัมน์ด้านขวา',
            'table_delete_column' => 'ลบคอลัมน์',
            'table_add_row_before' => 'เพิ่มแถวด้านบน',
            'table_add_row_after' => 'เพิ่มแถวด้านล่าง',
            'table_delete_row' => 'ลบแถว',
            'table_merge_cells' => 'รวมเซลล์',
            'table_split_cell' => 'แยกเซลล์',
            'table_toggle_header_row' => 'แสดง/ซ่อนหัวตาราง',
            'underline' => 'ขีดเส้นใต้',
            'undo' => 'ยกเลิก',
        ],
    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Create',

                'modal' => [

                    'heading' => 'สร้าง',

                    'actions' => [

                        'create' => [
                            'label' => 'สร้าง',
                        ],

                        'create_another' => [
                            'label' => 'บันทึกและสร้างอีกรายการ',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Edit',

                'modal' => [

                    'heading' => 'แก้ไข',

                    'actions' => [

                        'save' => [
                            'label' => 'บันทึก',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'ใช่',
            'false' => 'ไม่ใช่',
        ],

        'loading_message' => 'กำลังโหลด...',

        'max_items_message' => 'สามารถเลือกได้เพียง :count เท่านั้น',

        'no_search_results_message' => 'ไม่มีตัวเลือกที่ตรงกับการค้นหา',

        'placeholder' => 'เลือก',

        'searching_message' => 'กำลังค้นหา...',

        'search_prompt' => 'เริ่มพิมพ์เพื่อค้นหา...',

    ],

    'tags_input' => [
        'placeholder' => 'แท็กใหม่',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'ซ่อนรหัสผ่าน',
            ],

            'show_password' => [
                'label' => 'แสดงรหัสผ่าน',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'ใช่',
            'false' => 'ไม่ใช่',
        ],

    ],

];
