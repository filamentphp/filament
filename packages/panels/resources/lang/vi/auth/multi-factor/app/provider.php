<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Ứng dụng xác thực',

            'below_content' => 'Sử dụng một ứng dụng bảo mật để tạo một mã tạm thời nhằm xác thực khi đăng nhập.',

            'messages' => [
                'enabled' => 'Đã bật',
                'disabled' => 'Đã tắt',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Sử dụng một mã từ ứng dụng xác thực của bạn',

        'code' => [

            'label' => 'Nhập mã có 6 chữ số từ ứng dụng xác thực',

            'validation_attribute' => 'mã',

            'actions' => [

                'use_recovery_code' => [
                    'label' => 'Sử dụng mã khôi phục để thay thế',
                ],

            ],

            'messages' => [

                'invalid' => 'Mã bạn đã nhập không hợp lệ.',

            ],

        ],

        'recovery_code' => [

            'label' => 'Hoặc, nhập vào một mã khôi phục',

            'validation_attribute' => 'mã khôi phục',

            'messages' => [

                'invalid' => 'Mã khôi phục bạn đã nhập không hợp lệ',

            ],

        ],

    ],

];
