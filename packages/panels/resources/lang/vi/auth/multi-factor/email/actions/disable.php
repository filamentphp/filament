<?php

return [

    'label' => 'Tắt',

    'modal' => [

        'heading' => 'Tắt mã xác thực email',

        'description' => 'Bạn có chắc chắn muốn tắt mã xác thực email không? Tắt sẽ loại bỏ thêm một lớp bảo mật khác khỏi tài khoản của bạn.',

        'form' => [

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

        'actions' => [

            'submit' => [
                'label' => 'Tắt mã xác thực email',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Mã xác thực email đã bị tắt',
        ],

    ],

];
