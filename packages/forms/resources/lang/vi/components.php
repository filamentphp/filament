<?php

return [

    'builder' => [

        'actions' => [

            'clone' => [
                'label' => 'Sao chép',
            ],

            'add' => [

                'label' => 'Thêm vào :label',

                'modal' => [

                    'heading' => 'Thêm vào :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Thêm',
                        ],

                    ],

                ],

            ],

            'add_between' => [

                'label' => 'Chèn vào giữa các khối',

                'modal' => [

                    'heading' => 'Thêm vào :label',

                    'actions' => [

                        'add' => [
                            'label' => 'Thêm',
                        ],

                    ],

                ],

            ],

            'delete' => [
                'label' => 'Xóa',
            ],

            'edit' => [

                'label' => 'Chỉnh sửa',

                'modal' => [

                    'heading' => 'Chỉnh sửa khối',

                    'actions' => [

                        'save' => [
                            'label' => 'Lưu thay đổi',
                        ],

                    ],

                ],

            ],

            'reorder' => [
                'label' => 'Di chuyển',
            ],

            'move_down' => [
                'label' => 'Di chuyển xuống',
            ],

            'move_up' => [
                'label' => 'Di chuyển lên',
            ],

            'collapse' => [
                'label' => 'Thu gọn',
            ],

            'expand' => [
                'label' => 'Mở rộng',
            ],

            'collapse_all' => [
                'label' => 'Thu gọn tất cả',
            ],

            'expand_all' => [
                'label' => 'Mở rộng tất cả',
            ],

        ],

    ],

    'checkbox_list' => [

        'actions' => [

            'deselect_all' => [
                'label' => 'Bỏ chọn tất cả',
            ],

            'select_all' => [
                'label' => 'Chọn tất cả',
            ],

        ],

    ],

    'file_upload' => [

        'editor' => [

            'actions' => [

                'cancel' => [
                    'label' => 'Hủy thao tác',
                ],

                'drag_crop' => [
                    'label' => 'Chế độ kéo để cắt hẳn (crop)',
                ],

                'drag_move' => [
                    'label' => 'Chế độ kéo để di chuyển (move)',
                ],

                'flip_horizontal' => [
                    'label' => 'Lật ảnh theo chiều ngang',
                ],

                'flip_vertical' => [
                    'label' => 'Lật ảnh theo chiều dọc',
                ],

                'move_down' => [
                    'label' => 'Di chuyển ảnh xuống dưới',
                ],

                'move_left' => [
                    'label' => 'Di chuyển ảnh sang trái',
                ],

                'move_right' => [
                    'label' => 'Di chuyển ảnh sang phải',
                ],

                'move_up' => [
                    'label' => 'Di chuyển ảnh lên trên',
                ],

                'reset' => [
                    'label' => 'Đặt lại',
                ],

                'rotate_left' => [
                    'label' => 'Xoay ảnh sang trái',
                ],

                'rotate_right' => [
                    'label' => 'Xoay ảnh sang phải',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Đặt tỷ lệ khung hình thành :ratio',
                ],

                'save' => [
                    'label' => 'Lưu',
                ],

                'zoom_100' => [
                    'label' => 'Phóng to ảnh lên 100%',
                ],

                'zoom_in' => [
                    'label' => 'Phóng to',
                ],

                'zoom_out' => [
                    'label' => 'Thu nhỏ',
                ],

            ],

            'fields' => [

                'height' => [
                    'label' => 'Chiều cao',
                    'unit' => 'px',
                ],

                'rotation' => [
                    'label' => 'Độ xoay',
                    'unit' => 'deg',
                ],

                'width' => [
                    'label' => 'Chiều rộng',
                    'unit' => 'px',
                ],

                'x_position' => [
                    'label' => 'X',
                    'unit' => 'px',
                ],

                'y_position' => [
                    'label' => 'Y',
                    'unit' => 'px',
                ],

            ],

            'aspect_ratios' => [

                'label' => 'Tỷ lệ khung hình',

                'no_fixed' => [
                    'label' => 'Tự do',
                ],

            ],

            'svg' => [

                'messages' => [
                    'confirmation' => 'Không khuyến nghị chỉnh sửa các tập tin SVG vì có thể làm mất chất lượng khi thay đổi kích thước.\n Bạn có chắc chắn muốn tiếp tục không?',
                    'disabled' => 'Đã tắt chức năng chỉnh sửa tập tin SVG vì có thể làm mất chất lượng khi thay đổi kích thước.',
                ],

            ],

        ],

    ],

    'key_value' => [

        'actions' => [

            'add' => [
                'label' => 'Thêm dòng',
            ],

            'delete' => [
                'label' => 'Xóa dòng',
            ],

            'reorder' => [
                'label' => 'Sắp xếp dòng',
            ],

        ],

        'fields' => [

            'key' => [
                'label' => 'Khóa',
            ],

            'value' => [
                'label' => 'Giá trị',
            ],

        ],

    ],

    'markdown_editor' => [

        'tools' => [
            'attach_files' => 'Đính kèm tệp',
            'blockquote' => 'Trích dẫn',
            'bold' => 'In đậm',
            'bullet_list' => 'Danh sách đánh dấu',
            'code_block' => 'Khối code',
            'heading' => 'Tiêu đề',
            'italic' => 'In nghiêng',
            'link' => 'Liên kết',
            'ordered_list' => 'Danh sách đánh số',
            'redo' => 'Làm lại',
            'strike' => 'Gạch ngang',
            'table' => 'Bảng',

            'undo' => 'Hoàn tác',
        ],

    ],

    'modal_table_select' => [

        'actions' => [

            'select' => [

                'label' => 'Chọn',

                'actions' => [

                    'select' => [
                        'label' => 'Chọn',
                    ],

                ],

            ],

        ],

    ],

    'radio' => [

        'boolean' => [
            'true' => 'Có',
            'false' => 'Không',
        ],

    ],

    'repeater' => [

        'actions' => [

            'add' => [
                'label' => 'Thêm vào :label',
            ],

            'add_between' => [
                'label' => 'Chèn vào giữa',
            ],

            'delete' => [
                'label' => 'Xóa',
            ],

            'clone' => [
                'label' => 'Sao chép',
            ],

            'reorder' => [
                'label' => 'Di chuyển',
            ],

            'move_down' => [
                'label' => 'Di chuyển xuống',
            ],

            'move_up' => [
                'label' => 'Di chuyển lên',
            ],

            'collapse' => [
                'label' => 'Thu gọn',
            ],

            'expand' => [
                'label' => 'Mở rộng',
            ],

            'collapse_all' => [
                'label' => 'Thu gọn tất cả',
            ],

            'expand_all' => [
                'label' => 'Mở rộng tất cả',
            ],

        ],

    ],

    'rich_editor' => [

        'actions' => [

            'attach_files' => [

                'label' => 'Tải tệp lên',

                'modal' => [

                    'heading' => 'Tải tệp lên',

                    'form' => [

                        'file' => [

                            'label' => [
                                'new' => 'Tệp',
                                'existing' => 'Thay thế tệp',
                            ],

                        ],

                        'alt' => [

                            'label' => [
                                'new' => 'Văn bản thay thế (alt text)',
                                'existing' => 'Thay đổi văn bản thay thế',
                            ],

                        ],

                    ],

                ],

            ],

            'custom_block' => [

                'modal' => [

                    'actions' => [

                        'insert' => [
                            'label' => 'Chèn',
                        ],

                        'save' => [
                            'label' => 'Lưu',
                        ],

                    ],

                ],

            ],

            'link' => [

                'label' => 'Chỉnh sửa',

                'modal' => [

                    'heading' => 'Liên kết',

                    'form' => [

                        'url' => [
                            'label' => 'URL',
                        ],

                        'should_open_in_new_tab' => [
                            'label' => 'Mở trong tab mới',
                        ],

                    ],

                ],

            ],

        ],

        'no_merge_tag_search_results_message' => 'Không tìm thấy thẻ nội dung động nào.',

        'tools' => [
            'align_center' => 'Canh giữa',
            'align_end' => 'Canh đều bên phải',
            'align_justify' => 'Canh đều hai bên',
            'align_start' => 'Canh đều bên trái',
            'attach_files' => 'Đính kèm tệp',
            'blockquote' => 'Trích dẫn',
            'bold' => 'In đậm',
            'bullet_list' => 'Danh sách đánh dấu',
            'clear_formatting' => 'Xóa định dạng',
            'code' => 'Code',
            'code_block' => 'Khối code',
            'custom_blocks' => 'Khối tùy chỉnh',
            'details' => 'Khối chi tiết',
            'h1' => 'Tựa đề',
            'h2' => 'Tiêu đề',
            'h3' => 'Tiêu đề phụ',
            'highlight' => 'Nổi bật',
            'horizontal_rule' => 'Thước ngang',
            'italic' => 'In nghiêng',
            'lead' => 'Đoạn dẫn',
            'link' => 'Liên kết',
            'merge_tags' => 'Thẻ nội dung động',
            'ordered_list' => 'Danh sách đánh số',
            'redo' => 'Làm lại',
            'small' => 'Chữ nhỏ',
            'strike' => 'Gạch ngang',
            'subscript' => 'Chỉ số dưới',
            'superscript' => 'Chỉ số trên',
            'table' => 'Chèn bảng',
            'table_delete' => 'Xóa bảng',
            'table_add_column_before' => 'Thêm cột vào phía trước',
            'table_add_column_after' => 'Thêm cột vào phía sau',
            'table_delete_column' => 'Xóa cột',
            'table_add_row_before' => 'Thêm dòng vào phía trên',
            'table_add_row_after' => 'Thêm dòng vào phía dưới',
            'table_delete_row' => 'Xóa dòng',
            'table_merge_cells' => 'Gộp ô',
            'table_split_cell' => 'Chia ô',
            'table_toggle_header_row' => 'Bật/tắt dòng tiêu đề',
            'underline' => 'Gạch chân',
            'undo' => 'Hoàn tác',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

                'label' => 'Tạo',

                'modal' => [

                    'heading' => 'Tạo',

                    'actions' => [

                        'create' => [
                            'label' => 'Tạo',
                        ],

                        'create_another' => [
                            'label' => 'Tạo & tạo thêm',
                        ],

                    ],

                ],

            ],

            'edit_option' => [

                'label' => 'Chỉnh sửa',

                'modal' => [

                    'heading' => 'Chỉnh sửa',

                    'actions' => [

                        'save' => [
                            'label' => 'Lưu',
                        ],

                    ],

                ],

            ],

        ],

        'boolean' => [
            'true' => 'Có',
            'false' => 'Không',
        ],

        'loading_message' => 'Đang tải...',

        'max_items_message' => 'Chỉ có thể chọn :count mục.',

        'no_search_results_message' => 'Không có kết quả tìm kiếm phù hợp.',

        'placeholder' => 'Chọn một tùy chọn',

        'searching_message' => 'Đang tìm...',

        'search_prompt' => 'Bắt đầu gõ để tìm kiếm...',

    ],

    'tags_input' => [
        'placeholder' => 'Thêm thẻ mới',
    ],

    'text_input' => [

        'actions' => [

            'hide_password' => [
                'label' => 'Ẩn mật khẩu',
            ],

            'show_password' => [
                'label' => 'Hiện mật khẩu',
            ],

        ],

    ],

    'toggle_buttons' => [

        'boolean' => [
            'true' => 'Có',
            'false' => 'Không',
        ],

    ],

];
