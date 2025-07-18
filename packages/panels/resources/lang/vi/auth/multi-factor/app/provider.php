<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Ứng dụng xác thực',

            'below_content' => 'Sử dụng một ứng dụng an toàn để tạo mã tạm thời cho việc xác thực đăng nhập.',

            'messages' => [
                'enabled' => 'Đã kích hoạt',
                'disabled' => 'Đã tắt',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Sử dụng mã từ ứng dụng xác thực của bạn',

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

                'invalid' => 'Mã khôi phục bạn nhập không hợp lệ.',

            ],

        ],

    ],

];
