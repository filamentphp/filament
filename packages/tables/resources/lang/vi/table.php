<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'và còn :count',
        ],

        'messages' => [
            'copied' => 'Đã sao chép',
        ],

    ],

    'fields' => [

        'bulk_select_page' => [
            'label' => 'Chọn/bỏ chọn tất cả các mục cho tác vụ hàng loạt.',
        ],

        'bulk_select_record' => [
            'label' => 'Chọn/bỏ chọn mục :key cho các tác vụ hàng loạt.',
        ],

        'search_query' => [
            'label' => 'Tìm kiếm',
            'placeholder' => 'Tìm kiếm',
        ],

    ],

    'pagination' => [

        'label' => 'Điều hướng phân trang',

        'overview' => 'Hiển thị từ :first đến :last trong số :total kết quả',

        'fields' => [

            'records_per_page' => [

                'label' => 'mỗi trang',

                'options' => [
                    'all' => 'Tất cả',
                ],

            ],

        ],

        'buttons' => [

            'go_to_page' => [
                'label' => 'Đi tới trang :page',
            ],

            'next' => [
                'label' => 'Tiếp',
            ],

            'previous' => [
                'label' => 'Trước',
            ],

        ],

    ],

    'buttons' => [

        'disable_reordering' => [
            'label' => 'Sắp xếp lại bản ghi thành công',
        ],

        'enable_reordering' => [
            'label' => 'Sắp xếp lại bản ghi',
        ],

        'filter' => [
            'label' => 'Lọc',
        ],

        'open_actions' => [
            'label' => 'Xem thao tác',
        ],

        'toggle_columns' => [
            'label' => 'Chuyển đổi cột',
        ],

    ],

    'empty' => [

        'heading' => 'Không có dữ liệu nào',

        'buttons' => [

            'reset_column_searches' => [
                'label' => 'Xóa tìm kiếm cột',
            ],

        ],

    ],

    'filters' => [

        'buttons' => [

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

    'reorder_indicator' => 'Kéo và thả các bản ghi vào thứ tự.',

    'selection_indicator' => [

        'selected_count' => 'đã chọn 1 bản ghi.|đã chọn :count bản ghi.',

        'buttons' => [

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
