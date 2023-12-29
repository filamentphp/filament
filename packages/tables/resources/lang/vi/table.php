<?php

return [

    'column_toggle' => [

        'heading' => 'Cột',

    ],

    'columns' => [

        'text' => [

            'actions' => [
                'collapse_list' => 'Hiển thị :count ít hơn',
                'expand_list' => 'Hiển thị :count nhiều hơn',
            ],

            'more_list_items' => 'và :count cột khác',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Chọn/bỏ chọn tất cả các mục để thực hiện tác vụ hàng loạt.',
        ],

        'bulk_select_record' => [
            'label' => 'Chọn/bỏ chọn mục :key để thực hiện tác vụ hàng loạt.',
        ],

        'bulk_select_group' => [
            'label' => 'Chọn/bỏ chọn nhóm :title để thực hiện các hành động hàng loạt.',
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
                'label' => 'Số lượng',
            ],

            'sum' => [
                'label' => 'Tổng cộng',
            ],

        ],

    ],

    'actions' => [

        'disable_reordering' => [
            'label' => 'Hoàn thành việc sắp xếp lại bản ghi',
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
            'label' => 'Tác vụ hàng loạt',
        ],

        'toggle_columns' => [
            'label' => 'Chuyển đổi cột',
        ],

    ],

    'empty' => [

        'heading' => 'Không có :model nào',

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
                'label' => 'Đặt lại',
            ],

        ],

        'heading' => 'Bộ lọc',

        'indicator' => 'Bộ lọc hoạt động',

        'multi_select' => [
            'placeholder' => 'Tất cả',
        ],

        'select' => [
            'placeholder' => 'Tất cả',
        ],

        'trashed' => [

            'label' => 'Bản ghi đã xoá',

            'only_trashed' => 'Chỉ bản ghi đã xoá',

            'with_trashed' => 'Bao gồm bản ghi đã xóa',

            'without_trashed' => 'Không bao gồm bản ghi đã xóa',

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

    'reorder_indicator' => 'Kéo và thả các bản ghi để sắp xếp lại.',

    'selection_indicator' => [

        'selected_count' => '1 bản ghi đã chọn|:count bản ghi đã chọn',

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

                'label' => 'Hướng sắp xếp',

                'options' => [
                    'asc' => 'Tăng dần',
                    'desc' => 'Giảm dần',
                ],

            ],

        ],

    ],

];
