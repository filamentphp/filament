<?php

return [

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

        'filter' => [
            'label' => 'Lọc',
        ],

        'open_actions' => [
            'label' => 'Xem hành động',
        ],

    ],

    'actions' => [

        'modal' => [

            'requires_confirmation_subheading' => 'Bạn có chắc muốn thực hiện hành động này?',

            'buttons' => [

                'cancel' => [
                    'label' => 'Hủy',
                ],

                'confirm' => [
                    'label' => 'Xác nhận',
                ],

                'submit' => [
                    'label' => 'Gửi',
                ],

            ],

        ],

    ],

    'empty' => [
        'heading' => 'Không có dữ liệu nào',
    ],

    'selection_indicator' => [

        'buttons' => [

            'select_all' => [
                'label' => 'Chọn tất cả :count',
            ],

        ],

    ],

];
