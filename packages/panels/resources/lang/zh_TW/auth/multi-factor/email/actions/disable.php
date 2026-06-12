<?php

return [

    'label' => '關閉',

    'modal' => [

        'heading' => '停用電子郵件驗證碼',

        'description' => '您確定要停止接收電子郵件驗證碼嗎？停用此功能將從您的帳戶中移除額外的安全層。',

        'form' => [

            'code' => [

                'label' => '輸入我們透過電子郵件發送給您的 6 位數驗證碼',

                'validation_attribute' => '驗證碼',

                'actions' => [

                    'resend' => [

                        'label' => '透過電子郵件發送新驗證碼',

                        'notifications' => [

                            'resent' => [
                                'title' => '我們已透過電子郵件發送新驗證碼給您',
                            ],

                            'throttled' => [
                                'title' => '重新發送嘗試次數過多。請在請求另一個驗證碼前稍候。',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => '您輸入的驗證碼無效。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '停用電子郵件驗證碼',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => '電子郵件驗證碼已停用',
        ],

    ],

];
