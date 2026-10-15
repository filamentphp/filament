<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => '邮箱验证码',

            'below_content' => '登录时通过邮箱接收验证码，验证身份。',

            'messages' => [
                'enabled' => '已启用',
                'disabled' => '未启用',
            ],

        ],

    ],

    'login_form' => [

        'label' => '使用邮箱验证码',

        'code' => [

            'label' => '输入邮件中的 6 位验证码',

            'validation_attribute' => '验证码',

            'actions' => [

                'resend' => [

                    'label' => '重新发送验证码',

                    'notifications' => [

                        'resent' => [
                            'title' => '新验证码已发送，请查收邮件。',
                        ],

                        'throttled' => [
                            'title' => '发送过于频繁，请稍后再试。',
                        ],

                    ],

                ],

            ],

            'messages' => [

                'invalid' => '验证码无效。',

            ],

        ],

    ],

];
