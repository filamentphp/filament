<?php

return [

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

    'actions' => [

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

];
