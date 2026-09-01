<?php

return [

    'label' => 'Hồ sơ',

    'form' => [

        'email' => [
            'label' => 'Địa chỉ email',
        ],

        'name' => [
            'label' => 'Họ tên',
        ],

        'password' => [
            'label' => 'Mật khẩu mới',
            'validation_attribute' => 'mật khẩu',
        ],

        'password_confirmation' => [
            'label' => 'Xác nhận mật khẩu mới',
            'validation_attribute' => 'xác nhận mật khẩu',
        ],

        'current_password' => [
            'label' => 'Mật khẩu hiện tại',
            'below_content' => 'Vì lý do an toàn, vui lòng xác nhận mật khẩu của bạn để tiếp tục.',
            'validation_attribute' => 'mật khẩu hiện tại',
        ],

        'actions' => [

            'save' => [
                'label' => 'Lưu thay đổi',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Bảo mật 2 bước (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Đã gửi yêu cầu đổi địa chỉ email',
            'body' => 'Yêu cầu đổi địa chỉ email của bạn đã được gửi tới :email. Vui lòng kiểm tra email để xác nhận thay đổi.',
        ],

        'saved' => [
            'title' => 'Đã lưu',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'Hủy thao tác',
        ],

    ],

];
