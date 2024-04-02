<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'ทำสำเนา',
            ],

            'add' => [
                'label' => 'เพิ่มไปยัง :label',
            ],

            'add_between' => [
                'label' => 'แทรกระหว่างบล็อก',
            ],

            'delete' => [
                'label' => 'ลบ',
            ],

            'reorder' => [
                'label' => 'ย้าย',
            ],

            'move_down' => [
                'label' => 'ย้ายลง',
            ],

            'move_up' => [
                'label' => 'ย้ายขึ้น',
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
                    'label' => 'Rotation',
                    'unit' => 'องศา',
                ],

                'width' => [
                    'label' => 'Width',
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

        'toolbar_buttons' => [
            'attach_files' => 'แนบไฟล์',
            'blockquote' => 'บล็อกคำพูด',
            'bold' => 'ตัวหนา',
            'bullet_list' => 'รายการสัญลักษณ์แสดงหัวข้อย่อย',
            'code_block' => 'บล็อกโค้ด',
            'heading' => 'หัวข้อ',
            'italic' => 'ตัวเอียง',
            'link' => 'ลิงก์',
            'ordered_list' => 'รายการลําดับเลข',
            'redo' => 'กลับคืนสู่ปัจจุบัน',
            'strike' => 'ขีดฆ่า',
            'table' => 'ตาราง',
            'undo' => 'ย้อนกลับ',
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
                'label' => 'เพิ่มไปยัง :label',
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
                'label' => 'ย้ายลง',
            ],
            'move_up' => [
                'label' => 'ย้ายขึ้น',
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

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'เชื่อมโยง',
                    'unlink' => 'ยกเลิกการเชื่อมโยง',
                ],

                'label' => 'URL',

                'placeholder' => 'ป้อน URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'แนบไฟล์',
            'blockquote' => 'บล็อกคำพูด',
            'bold' => 'ตัวหนา',
            'bullet_list' => 'รายการสัญลักษณ์แสดงหัวข้อย่อย',
            'code_block' => 'บล็อกโค้ด',
            'h1' => 'ชื่อ',
            'h2' => 'หัวข้อ',
            'h3' => 'หัวข้อย่อย',
            'italic' => 'ตัวเอียง',
            'link' => 'ลิงก์',
            'ordered_list' => 'รายการลําดับเลข',
            'redo' => 'กลับคืนสู่ปัจจุบัน',
            'strike' => 'ขีดฆ่า',
            'underline' => 'ขีดเส้นใต้',
            'undo' => 'ย้อนกลับ',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'modal' => [

                    'heading' => 'Create',

                    'actions' => [

                        'create' => [
                            'label' => 'สร้าง',
                        ],

                        'create_another' => [
                            'label' => 'สร้างและสร้างอีก',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

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

        'placeholder' => 'เลือกตัวเลือก',

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

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'ย้อนกลับ',
            ],

            'next_step' => [
                'label' => 'ถัดไป',
            ],

        ],

    ],

];
