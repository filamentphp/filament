<?php

return [

    'title' => 'Sửa :label',

    'breadcrumb' => 'Sửa',

    'actions' => [

        'delete' => [

            'label' => 'Xóa',

            'modal' => [

                'heading' => 'Xóa :label',

                'subheading' => 'Bạn có chắc muốn thực hiện hành động này?',

                'buttons' => [

                    'delete' => [
                        'label' => 'Xóa',
                    ],

                ],

            ],

            'messages' => [
                'deleted' => 'Đã xóa',
            ],

        ],

        'view' => [
            'label' => 'Xem',
        ],

    ],

    'form' => [

        'actions' => [

            'cancel' => [
                'label' => 'Hủy',
            ],

            'save' => [
                'label' => 'Lưu',
            ],

        ],

    ],

    'messages' => [
        'saved' => 'Đã lưu',
    ],

];
