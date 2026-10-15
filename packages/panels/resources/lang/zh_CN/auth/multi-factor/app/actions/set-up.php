<?php

return [

    'label' => '设置',

    'modal' => [

        'heading' => '设置身份验证器',

        'description' => <<<'BLADE'
            请先安装 Google Authenticator 等身份验证器应用（<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>、<x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>）。
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => '使用身份验证器扫描此二维码：',

                'alt' => '用于设置身份验证器的二维码',

            ],

            'text_code' => [

                'instruction' => '也可以手动输入以下密钥：',

                'messages' => [
                    'copied' => '已复制',
                ],

            ],

            'recovery_codes' => [

                'instruction' => '请妥善保存以下恢复码。这些恢复码仅显示一次，无法使用身份验证器时，可用它们验证身份：',

            ],

        ],

        'form' => [

            'password' => [
                'label' => '当前密码',
                'validation_attribute' => '当前密码',
            ],

            'code' => [

                'label' => '输入验证器中的 6 位验证码',

                'validation_attribute' => '验证码',

                'below_content' => '登录或执行敏感操作时，需要输入验证器中的 6 位验证码。',

                'messages' => [

                    'invalid' => '验证码无效。',

                    'rate_limited' => '尝试次数过多，请稍后再试。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '启用身份验证器',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => '身份验证器已启用',
        ],

    ],

];
