<?php

return [

    'single' => [

        'label' => 'Xóa',

        'modal' => [

            'heading' => 'Xóa :label',

            'actions' => [

                'delete' => [
                    'label' => 'Xóa',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Đã xóa',
            ],

        ],

    ],

    'multiple' => [

        'label' => 'Xoá các mục đã chọn',

        'modal' => [

            'heading' => 'Xóa các mục :label đã chọn',

            'actions' => [

                'delete' => [
                    'label' => 'Xóa',
                ],

            ],

        ],

        'notifications' => [

            'deleted' => [
                'title' => 'Đã xóa',
            ],


            'deleted_partial' => [
                'title' => 'Đã xóa :count của :total',
                'missing_authorization_failure_message' => 'Bạn không có quyền xóa :count.',
                'missing_processing_failure_message' => ':count không thể xóa.',
            ],

            'deleted_none' => [
                'title' => 'Không thể xóa',
                'missing_authorization_failure_message' => 'Bạn không có quyền xóa :count.',
                'missing_processing_failure_message' => ':count không thể xóa.',
            ],

        ],

    ],

];
