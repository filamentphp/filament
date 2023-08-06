<?php

return [

    'column_toggle' => [

        'heading' => 'Cột',

    ],

    'columns' => [

        'text' => [
            'more_list_items' => 'và còn :count',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Chọn/bỏ chọn tất cả các mục cho tác vụ hàng loạt.',
        ],

        'bulk_select_record' => [
            'label' => 'Chọn/bỏ chọn mục :key cho các tác vụ hàng loạt.',
        ],

        'search' => [
            'label' => 'Tìm kiếm',
            'placeholder' => 'Tìm kiếm',
            'indicator' => 'Tìm kiếm',
        ],

    ],

    'summary' => [

        'heading' => 'Tóm tắt',

        'subheadings' => [
            'all' => 'Tất cả :label',
            'group' => 'Tóm tắt :group',
            'page' => 'Trang này',
        ],

        'summarizers' => [

            'average' => [
                'label' => 'Trung bình',
            ],

            'count' => [
                'label' => 'Đếm',
            ],

            'sum' => [
                'label' => 'Tổng',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Sắp xếp lại bản ghi thành công',
        ],

        'enable_reordering' => [
            'label' => 'Sắp xếp lại bản ghi',
        ],

        'filter' => [
            'label' => 'Lọc',
        ],

        'group' => [
            'label' => 'Nhóm',
        ],

        'open_bulk_actions' => [
            'label' => 'Xem thao tác',
        ],

        'toggle_columns' => [
            'label' => 'Chuyển đổi cột',
        ],

    ],

    'empty' => [

        'heading' => 'Không có dữ liệu nào',

        'description' => 'Tạo một :model để bắt đầu.',
    ],

    'filters' => [

        'actions' => [

            'remove' => [
                'label' => 'Xóa bộ lọc',
            ],

            'remove_all' => [
                'label' => 'Xóa toàn bộ bộ lọc',
                'tooltip' => 'Xóa toàn bộ bộ lọc',
            ],

            'reset' => [
                'label' => 'Đặt lại bộ lọc',
            ],

        ],

        'heading' => 'Bộ lọc',

        'indicator' => 'Bộ lọc đang kích hoạt',

        'multi_select' => [
            'placeholder' => 'Tất cả',
        ],

        'select' => [
            'placeholder' => 'Tất cả',
        ],

        'trashed' => [

            'label' => 'Các bản ghi đã xoá',

            'only_trashed' => 'Chỉ các bản ghi đã xoá',

            'with_trashed' => 'Bao gồm các bản ghi đã xóa',

            'without_trashed' => 'Không bao gồm các bản ghi bị xóa',

        ],

    ],

    'grouping' => [

        'fields' => [

            'group' => [
                'label' => 'Nhóm theo',
                'placeholder' => 'Nhóm theo',
            ],

            'direction' => [

                'label' => 'Hướng nhóm',

                'options' => [
                    'asc' => 'Tăng dần',
                    'desc' => 'Giảm dần',
                ],

            ],

        ],

    ],

    'reorder_indicator' => 'Kéo và thả các bản ghi vào thứ tự.',

    'selection_indicator' => [

        'selected_count' => 'đã chọn 1 bản ghi|đã chọn :count bản ghi',

        'actions' => [

            'select_all' => [
                'label' => 'Chọn tất cả :count',
            ],

            'deselect_all' => [
                'label' => 'Bỏ chọn tất cả',
            ],

        ],

    ],

    'sorting' => [

        'fields' => [

            'column' => [
                'label' => 'Sắp xếp theo',
            ],

            'direction' => [

                'label' => 'Thứ tự sắp xếp',

                'options' => [
                    'asc' => 'Tăng dần',
                    'desc' => 'Giảm dần',
                ],

            ],

        ],

    ],

];
