<?php

return [

    'label' => 'Điều hướng phân trang',

    'overview' => '{1} Đang hiện 1 kết quả|[2,*] Đang hiện từ :first đến :last của :total kết quả',

    'fields' => [

        'records_per_page' => [

            'label' => 'Mỗi trang',

            'options' => [
                'all' => 'Tất cả',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Đầu tiên',
        ],

        'go_to_page' => [
            'label' => 'Đi tới trang :page',
        ],

        'last' => [
            'label' => 'Cuối cùng',
        ],

        'next' => [
            'label' => 'Tiếp theo',
        ],

        'previous' => [
            'label' => 'Trước đó',
        ],

    ],

];
