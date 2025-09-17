<?php

return [

    'label' => 'Xuất :label',

    'modal' => [

        'heading' => 'Xuất :label',

        'form' => [

            'columns' => [

                'label' => 'Các cột',

                'form' => [

                    'is_enabled' => [
                        'label' => 'Đã bật :column',
                    ],

                    'label' => [
                        'label' => 'Nhãn của :column',
                    ],

                ],

            ],

        ],

        'actions' => [

            'export' => [
                'label' => 'Xuất',
            ],

        ],

    ],

    'notifications' => [

        'completed' => [

            'title' => 'Quá trình xuất hoàn tất',

            'actions' => [

                'download_csv' => [
                    'label' => 'Tải xuống .csv',
                ],

                'download_xlsx' => [
                    'label' => 'Tải xuống .xlsx',
                ],

            ],

        ],

        'max_rows' => [
            'title' => 'Nội dung xuất quá lớn',
            'body' => 'Bạn không thể xuất nhiều hơn 1 hàng cùng lúc.|Bạn không thể xuất nhiều hơn :count hàng cùng lúc.',
        ],

        'started' => [
            'title' => 'Quá trình xuất đã bắt đầu',
            'body' => 'Quá trình xuất của bạn đã bắt đầu và sẽ xử lý 1 hàng trong nền.|Quá trình xuất của bạn đã bắt đầu và sẽ xử lý :count hàng trong nền.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
