<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => '電子郵件驗證碼',

            'below_content' => '在您的電子郵件地址接收臨時驗證碼，以在登入時驗證您的身份。',

            'messages' => [
                'enabled' => '已啟用',
                'disabled' => '已停用',
            ],

        ],

    ],

    'login_form' => [

        'label' => '發送驗證碼至您的電子郵件',

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

];
