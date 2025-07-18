<?php

return [

    'label' => 'Tạo lại mã khôi phục',

    'modal' => [

        'heading' => 'Tạo lại mã khôi phục',

        'description' => 'Nếu bạn mất mã khôi phục, bạn có thể tạo lại chúng ở đây. Mã khôi phục cũ của bạn sẽ bị hủy ngay lập tức.',

        'form' => [

            'code' => [

                'label' => 'Nhập mã 6 chữ số từ ứng dụng xác thực',

                'validation_attribute' => 'mã xác thực',

                'messages' => [

                    'invalid' => 'Mã bạn nhập không hợp lệ.',

                ],

            ],

            'password' => [

                'label' => 'Hoặc, nhập mật khẩu hiện tại',

                'validation_attribute' => 'mật khẩu hiện tại',

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
            'title' => 'Mã khôi phục mới đã được tạo',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => 'Mã khôi phục mới',

            'description' => 'Vui lòng lưu các mã khôi phục này ở một nơi an toàn. Chúng sẽ chỉ được hiển thị một lần, nhưng bạn sẽ cần chúng nếu bạn mất quyền truy cập vào ứng dụng xác thực của mình:',

            'actions' => [

                'submit' => [
                    'label' => 'Đóng',
                ],

            ],

        ],

    ],

];
