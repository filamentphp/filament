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
                'title' => 'Đã khôi phục :count của :total',
                'missing_authorization_failure_message' => 'Bạn không có quyền khôi phục :count.',
                'missing_processing_failure_message' => ':count không thể khôi phục.',
            ],

            'restored_none' => [
                'title' => 'Không thể khôi phục',
                'missing_authorization_failure_message' => 'Bạn không có quyền khôi phục :count.',
                'missing_processing_failure_message' => ':count không thể khôi phục.',
            ],

        ],

    ],

];
