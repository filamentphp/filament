<?php

return [

    'label' => 'Xuất :label',

    'modal' => [

        'heading' => 'Xuất :label',

        'form' => [

            'columns' => [

                'label' => 'Cột',

                'form' => [

                    'is_enabled' => [
                        'label' => ':column đã kích hoạt',
                    ],

                    'label' => [
                        'label' => ':column nhãn',
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

            'title' => 'Xuất đã hoàn thành',

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
            'title' => 'Xuất quá lớn',
            'body' => 'Bạn không thể xuất nhiều hơn 1 hàng cùng lúc.|Bạn không thể xuất nhiều hơn :count hàng cùng lúc.',
        ],

        'started' => [
            'title' => 'Xuất đã bắt đầu',
            'body' => 'Quá trình xuất của bạn đã bắt đầu và 1 hàng sẽ được xử lý ở nền.|Quá trình xuất của bạn đã bắt đầu và :count hàng sẽ được xử lý ở nền.',
        ],

    ],

    'file_name' => 'export-:export_id-:model',

];
