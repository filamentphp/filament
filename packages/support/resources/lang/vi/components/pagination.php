<?php

return [

    'label' => 'Điều hướng phân trang',

    'overview' => '{1} Đang hiển thị 1 kết quả|[2,*] Đang hiển thị từ :first đến :last trong tổng số :total kết quả',

    'fields' => [

        'records_per_page' => [

            'label' => 'Mỗi trang',

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
            'label' => 'Tiếp theo',
        ],

        'previous' => [
            'label' => 'Trước đó',
        ],

    ],

];
