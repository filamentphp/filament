<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Mã xác thực email',

            'below_content' => 'Nhận mã tạm thời tại địa chỉ email của bạn để xác thực danh tính của bạn khi đăng nhập.',

            'messages' => [
                'enabled' => 'Đã kích hoạt',
                'disabled' => 'Đã tắt',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Gửi mã đến email của bạn',

        'code' => [

            'label' => 'Nhập mã 6 chữ số từ email chúng tôi gửi cho bạn',

            'validation_attribute' => 'mã xác thực',

            'actions' => [

                'resend' => [

                    'label' => 'Gửi mã mới qua email',

                    'notifications' => [

                        'resent' => [
                            'title' => 'Chúng tôi đã gửi cho bạn mã mới qua email',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => 'Mã bạn nhập không hợp lệ.',

            ],

        ],

    ],

];
