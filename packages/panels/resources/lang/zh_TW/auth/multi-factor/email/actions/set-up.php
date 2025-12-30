<?php

return [

    'label' => '設定',

    'modal' => [

        'heading' => '設定電子郵件驗證碼',

        'description' => '您每次登入或執行敏感操作時，都需要輸入我們透過電子郵件發送給您的 6 位數驗證碼。請檢查您的電子郵件以獲取 6 位數驗證碼來完成設定。',

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
                'label' => '啟用電子郵件驗證碼',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => '電子郵件驗證碼已啟用',
        ],

    ],

];
