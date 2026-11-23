<?php

return [

    'label' => '停用',

    'modal' => [

        'heading' => '停用電郵驗證碼',

        'description' => '確定要停止接收電郵驗證碼嗎？停用後，你的帳戶會少一重保障。',

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
                'label' => '停用電郵驗證碼',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => '電郵驗證碼已停用',
        ],

    ],

];
