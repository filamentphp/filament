<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => '電郵驗證碼',

            'below_content' => '登入時透過電郵接收臨時驗證碼，以核實身分。',

            'messages' => [
                'enabled' => '已啟用',
                'disabled' => '已停用',
            ],

        ],

    ],

    'login_form' => [

        'label' => '以電郵接收驗證碼',

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

            ],

        ],

    ],

];
