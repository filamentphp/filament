<?php

return [

    'label' => '停用',

    'modal' => [

        'heading' => '停用邮箱验证码',

        'description' => '确定停用邮箱验证码吗？停用后，账号将少一重安全保护。',

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
                'label' => '停用邮箱验证码',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => '邮箱验证码已停用',
        ],

    ],

];
