<?php

return [

    'label' => 'Tắt',

    'modal' => [

        'heading' => 'Tắt ứng dụng xác thực',

        'description' => 'Bạn có chắc chắn muốn ngừng sử dụng ứng dụng xác thực không? Làm vậy sẽ bỏ bớt một lớp bảo mật ra khỏi tài khoản.',

        'form' => [

            'code' => [

                'label' => 'Nhập mã 6 số từ ứng dụng xác thực',

                'validation_attribute' => 'mã',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => 'Sử dụng mã khôi phục để thay thế',
                    ],

                ],

                'messages' => [

                    'invalid' => 'Mã bạn vừa nhập không hợp lệ.',

                ],

            ],

            'recovery_code' => [

                'label' => 'Hoặc, nhập mã khôi phục',

                'validation_attribute' => 'mã khôi phục',

                'messages' => [

                    'invalid' => 'Mã khôi phục bạn vừa nhập không hợp lệ.',

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
            'title' => 'Ứng dụng xác thực đã được tắt',
        ],

    ],

];
