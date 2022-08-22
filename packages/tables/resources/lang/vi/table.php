<?php

return [

    'columns' => [

        'tags' => [
            'more' => 'và còn :count',
        ],

    ],

    'fields' => [

        'search_query' => [
            'label' => 'Tìm kiếm',
            'placeholder' => 'Tìm kiếm',
        ],

    ],

    'pagination' => [

        'label' => 'Pagination Navigation',

        'overview' => 'Hiển thị từ :first đến :last trong số :total kết quả',

        'fields' => [

            'records_per_page' => [
                'label' => 'mỗi trang',
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
            'label' => 'Xem hành động',
        ],

        'toggle_columns' => [
            'label' => 'Chuyển đổi cột',
        ],

    ],

    'empty' => [
        'heading' => 'Không có dữ liệu nào',
    ],

    'filters' => [

        'buttons' => [

            'reset' => [
                'label' => 'Đặt lại bộ lọc',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'Tất cả',
        ],

        'select' => [
            'placeholder' => 'Tất cả',
        ],

        'trashed' => [

            'label' => 'Các bản ghi đã xoá',

            'only_trashed' => 'Chỉ bản ghi đã xoá',

            'with_trashed' => 'Với bản ghi đã xoá',

            'without_trashed' => 'Không có bản ghi bị xóa',

        ],

    ],

    'reorder_indicator' => 'Kéo và thả các bản ghi vào thứ tự.',

    'selection_indicator' => [

        'selected_count' => 'đã chọn 1 bản ghi.|:count bản ghi đã chọn.',

        'buttons' => [

            'select_all' => [
                'label' => 'Chọn tất cả :count',
            ],

            'deselect_all' => [
                'label' => 'Bỏ chọn tất cả',
            ],

        ],

    ],

];
