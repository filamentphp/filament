<?php

return [

    'label' => 'Tắt',

    'modal' => [

        'heading' => 'Tắt mã xác thực email',

        'description' => 'Bạn có chắc chắn muốn ngừng nhận mã xác thực qua email không? Việc tắt chức năng này sẽ loại bỏ một lớp bảo mật khỏi tài khoản của bạn.',

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
                'label' => 'Tắt mã xác thực email',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'Mã xác thực email đã được tắt',
        ],

    ],

];
