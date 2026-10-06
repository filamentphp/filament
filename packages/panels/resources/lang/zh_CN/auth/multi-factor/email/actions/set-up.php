<?php

return [

    'label' => '设置',

    'modal' => [

        'heading' => '设置邮箱验证码',

        'description' => '登录或执行敏感操作时，需要输入邮件中的 6 位验证码。请查看邮件，输入验证码完成设置。',

        'form' => [

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

                    'rate_limited' => '尝试次数过多，请稍后再试。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '启用邮箱验证码',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => '邮箱验证码已启用',
        ],

    ],

];
