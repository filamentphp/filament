<?php

return [

    'single' => [

        'label' => 'Xóa vĩnh viễn',

        'modal' => [

            'heading' => 'Xóa vĩnh viễn :label',

            'actions' => [

                'delete' => [
                    'label' => 'Xoá',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Đã xoá',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Xóa vĩnh viễn các mục đã chọn',

        'modal' => [

            'heading' => 'Xóa vĩnh viễn các mục :label đã chọn',

            'actions' => [

                'delete' => [
                    'label' => 'Xoá',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Đã xóa',
            ],

            'deleted_partial' => [
                'title' => 'Đã xóa :count trong tổng số :total mục',
                'missing_authorization_failure_message' => 'Bạn không có quyền xóa :count mục.',
                'missing_processing_failure_message' => 'Đã không thể xóa :count mục.',
            ],

            'deleted_none' => [
                'title' => 'Việc xóa đã thất bại',
                'missing_authorization_failure_message' => 'Bạn không có quyền xóa :count mục.',
                'missing_processing_failure_message' => 'Đã không thể xóa :count mục.',
            ],

        ],

    ],

];
