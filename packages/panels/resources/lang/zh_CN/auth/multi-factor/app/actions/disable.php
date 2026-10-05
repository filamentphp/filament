<?php

return [

    'label' => '停用',

    'modal' => [

        'heading' => '停用身份验证器',

        'description' => '确定停用身份验证器吗？停用后，账号将少一重安全保护。',

        'form' => [
            'password' => [
                'label' => '当前密码',
                'validation_attribute' => '当前密码',
            ],

            'code' => [

                'label' => '输入验证器中的 6 位验证码',

                'validation_attribute' => '验证码',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => '改用恢复码',
                    ],

                ],

                'messages' => [

                    'invalid' => '验证码无效。',

                    'rate_limited' => '尝试次数过多，请稍后再试。',

                ],

            ],

            'recovery_code' => [

                'label' => '也可以输入恢复码',

                'validation_attribute' => '恢复码',

                'messages' => [

                    'invalid' => '恢复码无效。',

                    'rate_limited' => '尝试次数过多，请稍后再试。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '停用身份验证器',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => '身份验证器已停用',
        ],

    ],

];
