<?php

return [

    'label' => '關閉',

    'modal' => [

        'heading' => '停用信箱驗證',

        'description' => '您確定要停止使用信箱驗證嗎？停用此功能將會降低安全性。',

        'form' => [

            'code' => [

                'label' => '輸入來自信箱的 6 位數驗證碼',

                'validation_attribute' => '驗證碼',

                'actions' => [

                    'resend' => [

                        'label' => '發送新的驗證碼至信箱',

                        'notifications' => [

                            'resent' => [
                                'title' => '我們已經發送了一封新的驗證碼到您的信箱',
                            ],

                            'throttled' => [
                                'title' => '重新發送嘗試次數過多。請稍後再試。',
                            ],

                        ],

                    ],
                    'use_recovery_code' => [
                        'label' => '改用復原碼',
                    ],

                ],

                'messages' => [

                    'invalid' => '您輸入的驗證碼無效。',

                ],

            ],

            'recovery_code' => [

                'label' => '或，輸入復原碼',

                'validation_attribute' => '復原碼',

                'messages' => [

                    'invalid' => '您輸入的復原碼無效。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '停用信箱驗證',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => '信箱驗證已停用',
        ],

    ],

];
