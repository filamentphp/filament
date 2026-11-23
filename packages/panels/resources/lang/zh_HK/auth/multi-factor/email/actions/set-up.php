<?php

return [

    'label' => '設定',

    'modal' => [

        'heading' => '設定電郵驗證碼',

        'description' => '每次登入或執行敏感操作時，你都須輸入電郵中的 6 位數字驗證碼。請查閱電郵並輸入驗證碼，以完成設定。',

        'form' => [

            'code' => [

                'label' => '輸入電郵中的 6 位數字驗證碼',

                'validation_attribute' => '驗證碼',

                'actions' => [

                    'resend' => [

                        'label' => '以電郵傳送新驗證碼',

                        'notifications' => [

                            'resent' => [
                                'title' => '新驗證碼已透過電郵傳送給你',
                            ],

                            'throttled' => [
                                'title' => '重發次數過多，請稍後再索取驗證碼。',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => '你輸入的驗證碼無效。',

                    'rate_limited' => '嘗試次數過多，請稍後再試。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '啟用電郵驗證碼',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => '電郵驗證碼已啟用',
        ],

    ],

];
