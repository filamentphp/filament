<?php

return [

    'single' => [

        'label' => 'Khôi phục',

        'modal' => [

            'heading' => 'Khôi phục :label',

            'actions' => [

                'restore' => [
                    'label' => 'Khôi phục',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Đã khôi phục',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Khôi phục các mục đã chọn',

        'modal' => [

            'heading' => 'Khôi phục các mục :label đã chọn',

            'actions' => [

                'restore' => [
                    'label' => 'Khôi phục',
                ],

            ],

        ],

        'notifications' => [

            'restored' => [
                'title' => 'Đã khôi phục',
            ],

            'restored_partial' => [
                'title' => 'Đã khôi phục :count trong tổng số :total mục',
                'missing_authorization_failure_message' => 'Bạn không có quyền khôi phục :count mục.',
                'missing_processing_failure_message' => 'Đã không thể khôi phục :count mục.',
            ],

            'restored_none' => [
                'title' => 'Việc khôi phục thất bại',
                'missing_authorization_failure_message' => 'Bạn không có quyền khôi phục :count mục.',
                'missing_processing_failure_message' => 'Đã không thể khôi phục :count mục.',
            ],

        ],

    ],

];
