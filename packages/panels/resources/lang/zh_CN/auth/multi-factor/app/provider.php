<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => '身份验证器',

            'below_content' => '使用验证器生成动态验证码，登录时验证身份。',

            'messages' => [
                'enabled' => '已启用',
                'disabled' => '未启用',
            ],

        ],

    ],

    'login_form' => [

        'label' => '使用身份验证器',

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

            ],

        ],

        'recovery_code' => [

            'label' => '也可以输入恢复码',

            'validation_attribute' => '恢复码',

            'messages' => [

                'invalid' => '恢复码无效。',

            ],

        ],

    ],

];
