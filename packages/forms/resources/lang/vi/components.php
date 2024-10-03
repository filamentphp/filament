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
                    'label' => 'Huỷ bỏ',
                ],

                'drag_crop' => [
                    'label' => 'Chế độ kéo "cắt"',
                ],

                'drag_move' => [
                    'label' => 'Chế độ kéo "di chuyển"',
                ],

                'flip_horizontal' => [
                    'label' => 'Lật ảnh theo chiều ngang',
                ],

                'flip_vertical' => [
                    'label' => 'Lật ảnh theo chiều dọc',
                ],

                'move_down' => [
                    'label' => 'Di chuyển hình ảnh xuống',
                ],

                'move_left' => [
                    'label' => 'Di chuyển hình ảnh sang trái',
                ],

                'move_right' => [
                    'label' => 'Di chuyển hình ảnh sang phải',
                ],

                'move_up' => [
                    'label' => 'Di chuyển hình ảnh lên trên',
                ],

                'reset' => [
                    'label' => 'Đặt lại',
                ],

                'rotate_left' => [
                    'label' => 'Xoay hình ảnh sang trái',
                ],

                'rotate_right' => [
                    'label' => 'Xoay hình ảnh sang phải',
                ],

                'set_aspect_ratio' => [
                    'label' => 'Đặt tỷ lệ khung hình thành :ratio',
                ],

                'save' => [
                    'label' => 'Lưu',
                ],

                'zoom_100' => [
                    'label' => 'Phóng to hình ảnh lên 100%',
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
                    'confirmation' => 'Việc chỉnh sửa các tập tin SVG không được khuyến nghị vì có thể dẫn đến mất chất lượng khi thay đổi tỷ lệ.\n Bạn có chắc chắn muốn tiếp tục không?',
                    'disabled' => 'Việc chỉnh sửa các tập tin SVG bị vô hiệu hóa vì có thể dẫn đến mất chất lượng khi thay đổi tỷ lệ..',
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

        'toolbar_buttons' => [
            'attach_files' => 'Đính kèm tệp',
            'blockquote' => 'Trích dẫn',
            'bold' => 'In đậm',
            'bullet_list' => 'Danh sách đánh dấu',
            'code_block' => 'Khối mã',
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

        'dialogs' => [

            'link' => [

                'actions' => [
                    'link' => 'Liên kết',
                    'unlink' => 'Bỏ liên kết',
                ],

                'label' => 'URL',

                'placeholder' => 'Nhập URL',

            ],

        ],

        'toolbar_buttons' => [
            'attach_files' => 'Đính kèm tệp',
            'blockquote' => 'Trích dẫn',
            'bold' => 'In đậm',
            'bullet_list' => 'Danh sách đánh dấu',
            'code_block' => 'Khối mã',
            'h1' => 'Tiêu đề chính',
            'h2' => 'Tiêu đề',
            'h3' => 'Tiêu đề phụ',
            'italic' => 'In nghiêng',
            'link' => 'Liên kết',
            'ordered_list' => 'Danh sách đánh số',
            'redo' => 'Làm lại',
            'strike' => 'Gạch ngang',
            'underline' => 'Gạch chân',
            'undo' => 'Hoàn tác',
        ],

    ],

    'select' => [

        'actions' => [

            'create_option' => [

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

                'modal' => [

                    'heading' => 'Chỉnh sửa',

                    'actions' => [

                        'save' => [
                            'label' => 'Lưu lại',
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

        'placeholder' => 'Chọn một tuỳ chọn',

        'searching_message' => 'Đang tìm kiếm...',

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

    'wizard' => [

        'actions' => [

            'previous_step' => [
                'label' => 'Quay lại',
            ],

            'next_step' => [
                'label' => 'Tiếp theo',
            ],

        ],

    ],

];
