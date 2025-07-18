<?php

return [

    'label' => 'Thiết lập',

    'modal' => [

        'heading' => 'Thiết lập mã xác thực email',

        'description' => 'Bạn sẽ cần nhập mã 6 chữ số từ email chúng tôi gửi cho bạn mỗi khi đăng nhập hoặc thực hiện hành động nhạy cảm. Kiểm tra email của bạn để nhận mã 6 chữ số để hoàn thành thiết lập.',

        'form' => [

            'code' => [

                'label' => 'Nhập mã 6 chữ số từ email chúng tôi gửi cho bạn',

                'validation_attribute' => 'mã xác thực',

                'actions' => [

                    'resend' => [

                        'label' => 'Gửi lại mã qua email',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Chúng tôi đã gửi lại mã cho bạn qua email',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Mã bạn đã nhập không hợp lệ.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Kích hoạt mã xác thực email',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Mã xác thực email đã được kích hoạt',
        ],

    ],

];
