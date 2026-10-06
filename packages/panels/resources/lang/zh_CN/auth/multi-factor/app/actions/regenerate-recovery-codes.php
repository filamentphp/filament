<?php

return [

    'label' => '重新生成恢复码',

    'modal' => [

        'heading' => '重新生成身份验证器恢复码',

        'description' => '恢复码丢失后，您可以在这里重新生成。原有恢复码会立即失效。',

        'form' => [

            'code' => [

                'label' => '输入验证器中的 6 位验证码',

                'validation_attribute' => '验证码',

                'messages' => [

                    'invalid' => '验证码无效。',

                    'rate_limited' => '尝试次数过多，请稍后再试。',

                ],

            ],

            'password' => [

                'label' => '输入当前密码',

                'validation_attribute' => '密码',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '重新生成恢复码',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => '新的身份验证器恢复码已生成',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => '新的恢复码',

            'description' => '请妥善保存以下恢复码。这些恢复码仅显示一次，无法使用身份验证器时，可用它们验证身份：',

            'actions' => [

                'submit' => [
                    'label' => '关闭',
                ],

            ],

        ],

    ],

];
