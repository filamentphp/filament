<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'Mã xác thực email',

            'below_content' => 'Nhận một mã tạm thời qua email để xác minh danh tính của bạn khi đăng nhập.',

            'messages' => [
                'enabled' => 'Đã bật',
                'disabled' => 'Đã tắt',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'Gửi mã tới email của bạn',

        'code' => [

            'label' => 'Nhập mã 6 số chúng tôi đã gửi tới email của bạn',

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

];
