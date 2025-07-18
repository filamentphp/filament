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
            'below_content' => 'Vui lòng xác nhận mật khẩu để tiếp tục.',
            'validation_attribute' => 'mật khẩu hiện tại',
        ],

        'actions' => [

            'save' => [
                'label' => 'Lưu thay đổi',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => 'Xác thực hai yếu tố (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'Yêu cầu thay đổi địa chỉ email đã được gửi',
            'body' => 'Một yêu cầu thay đổi địa chỉ email đã được gửi đến :email. Vui lòng kiểm tra email của bạn để xác nhận thay đổi.',
        ],

        'saved' => [
            'title' => 'Đã lưu',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'quay lại',
        ],

    ],

];
