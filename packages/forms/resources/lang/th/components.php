<?php

return [

    'builder' => [

        'reordering' => [
            'item' => 'บล็อก',
            'position' => ':label ตำแหน่งที่ :position จาก :count',
            'moved' => 'ย้าย :label ไปยังตำแหน่งที่ :position จาก :count แล้ว',
        ],

        'actions' => [

            'clone' => [
                'label' => 'ทำสำเนา',
            ],

            'add' => [

                'label' => 'เพิ่มไปยัง :label',

                'modal' => [

                    'heading' => 'เพิ่มไปยัง :label',

                    'actions' => [

                        'add' => [
                            'label' => 'เพิ่ม',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'แทรกระหว่างบล็อก',

                'modal' => [

                    'heading' => 'เพิ่มไปยัง :label',

                    'actions' => [

                        'add' => [
                            'label' => 'เพิ่ม',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'ลบ',
            ],

            'edit' => [

                'label' => 'แก้ไข',

                'modal' => [

                    'heading' => 'แก้ไขบล็อก',

                    'actions' => [

                        'save' => [
                            'label' => 'บันทึกการเปลี่ยนแปลง',
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

        'block_picker' => [

            'no_search_results_message' => 'ไม่พบบล็อกที่ตรงกับการค้นหา',

            'search_prompt' => 'ค้นหาบล็อก',

        ],

    ],

    'checkbox_list' => [

        'required_description' => 'กรุณาเลือกอย่างน้อยหนึ่งตัวเลือก',

        'actions' => [

            'deselect_all' => [
                'label' => 'ยกเลิกการเลือกทั้งหมด',
            ],

            'select_all' => [
                'label' => 'เลือกทั้งหมด',
            ],

        ],

    ],

    'color_picker' => [

        'panel_label' => 'ตัวเลือกสี',

    ],

    'date_time_picker' => [

        'month_select' => [
            'label' => 'เดือน',
        ],

        'year_input' => [
            'label' => 'ปี',
        ],

        'hour_input' => [
            'label' => 'ชั่วโมง',
        ],

        'minute_input' => [
            'label' => 'นาที',
        ],

        'second_input' => [
            'label' => 'วินาที',
        ],

    ],

    'file_upload' => [

        'placeholder' => 'ยังไม่มีไฟล์ที่อัปโหลด',

        'actions' => [

            'download' => [
                'label' => 'ดาวน์โหลด',
            ],

            'open' => [
                'label' => 'เปิดในแท็บใหม่',
            ],

        ],

        'editor' => [

            'label' => 'ตัวแก้ไขรูปภาพ',

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
                    'unit' => 'พิกเซล',
                ],

                'rotation' => [
                    'label' => 'หมุน',
                    'unit' => 'องศา',
                ],

                'width' => [
                    'label' => 'ความกว้าง',
                    'unit' => 'พิกเซล',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => 'พิกเซล',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => 'พิกเซล',
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
                    'confirmation' => 'การแก้ไขไฟล์ SVG ไม่แนะนำ เนื่องจากอาจเกิดการสูญเสียคุณภาพเมื่อมีการปรับขนาด
แน่ใจหรือไม่ว่าต้องการดำเนินการต่อ?',
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

            'move_down' => [
                'label' => 'เลื่อนลง',
            ],

            'move_up' => [
                'label' => 'เลื่อนขึ้น',
            ],

            'reorder' => [
                'label' => 'จัดลำดับแถว',
            ],

        ],

        'columns' => [

            'actions' => [
                'label' => 'การดำเนินการ',
            ],

            'reorder' => [
                'label' => 'จัดลำดับ',
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

        'reordering' => [
            'row' => 'แถว',
            'position' => ':label ตำแหน่งที่ :position จาก :count',
            'moved' => 'ย้าย :label ไปยังตำแหน่งที่ :position จาก :count แล้ว',
        ],

    ],

    'markdown_editor' => [

        'file_attachments_accepted_file_types_message' => 'ไฟล์ที่อัปโหลดต้องเป็นประเภท: :values',

        'file_attachments_max_size_message' => 'ไฟล์ที่อัปโหลดต้องมีขนาดไม่เกิน :max กิโลไบต์',

        'tools' => [
            'attach_files' => 'แนบไฟล์',
            'blockquote' => 'บล็อกคำพูด',
            'bold' => 'ตัวหนา',
            'bullet_list' => 'รายการสัญลักษณ์แสดงหัวข้อย่อย',
            'code_block' => 'บล็อกโค้ด',
            'heading' => 'หัวข้อ',
            'italic' => 'ตัวเอียง',
            'link' => 'ลิงก์',
            'ordered_list' => 'รายการลำดับเลข',
            'redo' => 'ทำอีกครั้ง',
            'strike' => 'ขีดฆ่า',
            'table' => 'ตาราง',
            'undo' => 'ยกเลิก',
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

        'reordering' => [
            'item' => 'รายการ',
            'position' => ':label ตำแหน่งที่ :position จาก :count',
            'moved' => 'ย้าย :label ไปยังตำแหน่งที่ :position จาก :count แล้ว',
        ],

        'columns' => [

            'actions' => [
                'label' => 'การดำเนินการ',
            ],

            'reorder' => [
                'label' => 'จัดลำดับ',
            ],

        ],

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

                'label' => 'อัปโหลดไฟล์',

                'modal' => [

                    'heading' => 'อัปโหลดไฟล์',

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

            'close_panel' => [
                'label' => 'ปิดแผง',
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

            'grid' => [

                'label' => 'ตารางกริด',

                'modal' => [

                    'heading' => 'ตารางกริด',

                    'form' => [

                        'preset' => [

                            'label' => 'ค่าที่กำหนดไว้',

                            'placeholder' => 'ไม่มี',

                            'options' => [
                                'two' => 'สองคอลัมน์',
                                'three' => 'สามคอลัมน์',
                                'four' => 'สี่คอลัมน์',
                                'five' => 'ห้าคอลัมน์',
                                'two_start_third' => 'สองคอลัมน์ (เริ่มที่หนึ่งในสาม)',
                                'two_end_third' => 'สองคอลัมน์ (จบที่สองในสาม)',
                                'two_start_fourth' => 'สองคอลัมน์ (เริ่มที่หนึ่งในสี่)',
                                'two_end_fourth' => 'สองคอลัมน์ (จบที่สามในสี่)',
                            ],
                        ],

                        'columns' => [
                            'label' => 'คอลัมน์',
                        ],

                        'from_breakpoint' => [

                            'label' => 'จากจุดปรับขนาด (Breakpoint)',

                            'options' => [
                                'default' => 'ทั้งหมด',
                                'sm' => 'ขนาดเล็ก (sm)',
                                'md' => 'ขนาดกลาง (md)',
                                'lg' => 'ขนาดใหญ่ (lg)',
                                'xl' => 'ขนาดใหญ่พิเศษ (xl)',
                                '2xl' => 'ขนาดใหญ่พิเศษมาก (2xl)',
                            ],

                        ],

                        'is_asymmetric' => [
                            'label' => 'สองคอลัมน์ไม่สมมาตร',
                        ],

                        'start_span' => [
                            'label' => 'การขยายเริ่มต้น',
                        ],

                        'end_span' => [
                            'label' => 'การขยายสิ้นสุด',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'ลิงก์',

                'modal' => [

                    'heading' => 'ลิงก์',

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

            'text_color' => [

                'label' => 'สีข้อความ',

                'modal' => [

                    'heading' => 'สีข้อความ',

                    'form' => [

                        'color' => [
                            'label' => 'สี',

                            'options' => [
                                'slate' => 'เทาอมฟ้า',
                                'gray' => 'เทา',
                                'zinc' => 'เทาสังกะสี',
                                'neutral' => 'เทากลาง',
                                'stone' => 'เทาหิน',
                                'mauve' => 'ม่วงอมเทา',
                                'olive' => 'เขียวมะกอก',
                                'mist' => 'เทาหมอก',
                                'taupe' => 'น้ำตาลอมเทา',
                                'red' => 'แดง',
                                'orange' => 'ส้ม',
                                'amber' => 'อำพัน',
                                'yellow' => 'เหลือง',
                                'lime' => 'เขียวมะนาว',
                                'green' => 'เขียว',
                                'emerald' => 'มรกต',
                                'teal' => 'เขียวหัวเป็ด',
                                'cyan' => 'ฟ้าอมเขียว',
                                'sky' => 'ฟ้า',
                                'blue' => 'น้ำเงิน',
                                'indigo' => 'คราม',
                                'violet' => 'ม่วงไวโอเล็ต',
                                'purple' => 'ม่วง',
                                'fuchsia' => 'บานเย็น',
                                'pink' => 'ชมพู',
                                'rose' => 'ชมพูกุหลาบ',
                            ],
                        ],

                        'custom_color' => [
                            'label' => 'สีที่กำหนดเอง',
                        ],

                    ],

                ],

            ],

        ],

        'custom_blocks' => [

            'actions' => [

                'delete' => [
                    'label' => 'ลบบล็อก',
                ],

                'edit' => [
                    'label' => 'แก้ไขบล็อก',
                ],

            ],

            'no_search_results_message' => 'ไม่พบบล็อกที่ตรงกับการค้นหา',

            'search_label' => 'ค้นหาบล็อก',

            'search_prompt' => 'ค้นหาบล็อก',

        ],

        'file_attachments_accepted_file_types_message' => 'ไฟล์ที่อัปโหลดต้องเป็นประเภท: :values',

        'file_attachments_max_size_message' => 'ไฟล์ที่อัปโหลดต้องมีขนาดไม่เกิน :max กิโลไบต์',

        'no_merge_tag_search_results_message' => 'ไม่พบผลลัพธ์ของแท็กที่รวม',

        'mentions' => [
            'no_options_message' => 'ไม่มีตัวเลือกที่ใช้งานได้',
            'no_search_results_message' => 'ไม่พบผลลัพธ์ที่ตรงกับการค้นหาของคุณ',
            'search_prompt' => 'เริ่มพิมพ์เพื่อค้นหา...',
            'searching_message' => 'กำลังค้นหา...',
        ],

        'toolbar' => [
            'label' => 'แถบเครื่องมือตัวแก้ไข',
        ],

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
            'h2' => 'หัวข้อ 2',
            'h3' => 'หัวข้อ 3',
            'h4' => 'หัวข้อ 4',
            'h5' => 'หัวข้อ 5',
            'h6' => 'หัวข้อ 6',
            'grid' => 'ตารางกริด',
            'grid_delete' => 'ลบตารางกริด',
            'highlight' => 'เน้นข้อความ',
            'horizontal_rule' => 'เส้นคั่นแนวนอน',
            'italic' => 'ตัวเอียง',
            'lead' => 'ข้อความนำ',
            'link' => 'ลิงก์',
            'merge_tags' => 'รวมแท็ก',
            'ordered_list' => 'รายการแบบตัวเลข',
            'paragraph' => 'ย่อหน้า',
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
            'table_toggle_header_cell' => 'แสดง/ซ่อนหัวเซลล์',
            'text_color' => 'สีข้อความ',
            'underline' => 'ขีดเส้นใต้',
            'undo' => 'ยกเลิก',
        ],

        'uploading_file_message' => 'กำลังอัปโหลดไฟล์...',

    ],

    'select' => [

        'actions' => [

            'clear' => [
                'label' => 'ล้างที่เลือก',
            ],

            'create_option' => [

                'label' => 'สร้าง',

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

                'label' => 'แก้ไข',

                'modal' => [

                    'heading' => 'แก้ไข',

                    'actions' => [

                        'save' => [
                            'label' => 'บันทึก',
                        ],

                    ],

                ],

            ],

            'remove_option' => [
                'label' => 'ลบ :label',
            ],

        ],

        'boolean' => [
            'true' => 'ใช่',
            'false' => 'ไม่ใช่',
        ],

        'error_message' => 'ไม่สามารถโหลดตัวเลือกได้ กรุณาลองใหม่อีกครั้ง',

        'loading_message' => 'กำลังโหลด...',

        'max_items_message' => 'สามารถเลือกได้เพียง :count เท่านั้น',

        'no_options_message' => 'ไม่มีตัวเลือกที่ใช้งานได้',

        'no_search_results_message' => 'ไม่มีตัวเลือกที่ตรงกับการค้นหา',

        'placeholder' => 'เลือก',

        'required_description' => 'กรุณาเลือกหนึ่งตัวเลือก',

        'searching_message' => 'กำลังค้นหา...',

        'search_label' => 'ค้นหา',

        'search_prompt' => 'เริ่มพิมพ์เพื่อค้นหา...',

    ],

    'tags_input' => [

        'actions' => [

            'delete' => [
                'label' => 'ลบ',
            ],

        ],

        'placeholder' => 'แท็กใหม่',

        'tag_added' => 'เพิ่มแล้ว: :tag',

        'tag_removed' => 'ลบแล้ว: :tag',

    ],

    'text_input' => [

        'actions' => [

            'copy' => [
                'label' => 'คัดลอก',
                'message' => 'คัดลอกแล้ว',
            ],

            'hide_password' => [
                'label' => 'ซ่อนรหัสผ่าน',
            ],

            'show_password' => [
                'label' => 'แสดงรหัสผ่าน',
            ],

        ],

    ],

    'toggle_buttons' => [

        'required_description' => 'กรุณาเลือกอย่างน้อยหนึ่งตัวเลือก',

        'boolean' => [
            'true' => 'ใช่',
            'false' => 'ไม่ใช่',
        ],

    ],

];
