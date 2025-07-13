<?php

return [

    'label' => 'Tạo lại mã khôi phục',

    'modal' => [

        'heading' => 'Tạo lại mã khôi phục cho ứng dụng xác thực',

        'description' => 'Nếu đã làm mất mã khôi phục, bạn có thể tạo lại ở đây. Tất cả mã khôi phục cũ sẽ bị vô hiệu hóa ngay lập tức.',

        'form' => [

            'code' => [

                'label' => 'Nhập mã 6 số từ ứng dụng xác thực',

                'validation_attribute' => 'mã',

                'messages' => [

                    'invalid' => 'Mã bạn vừa nhập không hợp lệ.',

                ],

            ],

            'password' => [

                'label' => 'Hoặc, nhập mật khẩu hiện tại của bạn',

                'validation_attribute' => 'mật khẩu',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'Tạo lại mã khôi phục',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => 'Đã tạo mới các mã khôi phục cho ứng dụng xác thực',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Mã khôi phục mới',

            'description' => 'Vui lòng lưu các mã khôi phục này ở nơi an toàn. Bạn chỉ xem được một lần duy nhất là lúc này, và sẽ cần tới chúng nếu không mở được ứng dụng xác thực:',

            'actions' => [

                'submit' => [
                    'label' => 'Đóng',
                ],

            ],

        ],

    ],

];
