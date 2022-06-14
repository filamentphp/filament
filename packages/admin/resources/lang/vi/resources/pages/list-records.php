<?php

return [

    'breadcrumb' => 'Danh sách',

    'actions' => [

        'create' => [

            'label' => 'Thêm :label',

            'modal' => [

                'heading' => 'Tạo :label',

                'actions' => [

                    'create' => [
                        'label' => 'Tạo',
                    ],

                    'create_and_create_another' => [
                        'label' => 'Tạo & tiếp tục tạo mới',
                    ],

                ],

            ],

            'messages' => [
                'created' => 'Đã tạo',
            ],

        ],

    ],

    'table' => [

        'actions' => [

            'delete' => [

                'label' => 'Xoá',

                'messages' => [
                    'deleted' => 'Đã xoá',
                ],

            ],

            'edit' => [

                'label' => 'Sửa',

                'modal' => [

                    'heading' => 'Sửa :label',

                    'actions' => [

                        'save' => [
                            'label' => 'Lưu',
                        ],

                    ],

                ],

                'messages' => [
                    'saved' => 'Đã lưu',
                ],

            ],

            'view' => [
                'label' => 'Xem',
            ],

        ],

        'bulk_actions' => [

            'delete' => [

                'label' => 'Xoá mục đã chọn',

                'messages' => [
                    'deleted' => 'Đã xoá',
                ],

            ],

        ],

    ],

];
