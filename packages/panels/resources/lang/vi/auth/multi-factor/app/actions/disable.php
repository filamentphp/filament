<?php

return [

    'label' => 'Tắt',

    'modal' => [

        'heading' => 'Tắt ứng dụng xác thực',

        'description' => 'Bạn có chắc chắn muốn tắt ứng dụng xác thực không? Tắt sẽ loại bỏ thêm một lớp bảo mật khác khỏi tài khoản của bạn.',

        'form' => [

            'code' => [

                'label' => 'Nhập mã 6 chữ số từ ứng dụng xác thực',

                'validation_attribute' => 'mã xác thực',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Sử dụng mã khôi phục thay vì mã xác thực',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Mã bạn đã nhập không hợp lệ.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Hoặc, nhập mã khôi phục',

                'validation_attribute' => 'mã khôi phục',

                'messages' => [

                    'invalid' => 'Mã khôi phục bạn đã nhập không hợp lệ.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Tắt ứng dụng xác thực',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Ứng dụng xác thực đã bị tắt',
        ],

    ],

];
