<?php

return [

    'label' => 'Thiết lập',

    'modal' => [

        'heading' => 'Thiết lập mã xác thực email',

        'description' => 'Bạn sẽ cần nhập mã 6 số được gửi tới email của bạn mỗi khi đăng nhập hoặc thực hiện các thao tác nhạy cảm. Vui lòng kiểm tra email để lấy mã và hoàn tất quá trình thiết lập.',

        'form' => [

            'code' => [

                'label' => 'Nhập mã 6 số đã được gửi tới email của bạn',

                'validation_attribute' => 'mã',

                'actions' => [

                    'resend' => [

                        'label' => 'Gửi mã mới qua email',

                        'notifications' => [

                            'resent' => [
                                'title' => 'Chúng tôi đã gửi mã mới tới email của bạn',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => 'Mã bạn vừa nhập không hợp lệ.',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Bật mã xác thực email',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'Đã bật mã xác thực email',
        ],

    ],

];
