<?php

return [

    'label' => '停用',

    'modal' => [

        'heading' => '停用驗證器應用程式',

        'description' => '確定要停用驗證器應用程式嗎？停用後，你的帳戶會少一重保障。',

        'form' => [
            'password' => [
                'label' => '現有密碼',
                'validation_attribute' => '現有密碼',
            ],

            'code' => [

                'label' => '輸入驗證器應用程式顯示的 6 位數字驗證碼',

                'validation_attribute' => '驗證碼',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => '改用復原碼',
                    ],

                ],

                'messages' => [

                    'invalid' => '你輸入的驗證碼無效。',

                    'rate_limited' => '嘗試次數過多，請稍後再試。',

                ],

            ],

            'recovery_code' => [

                'label' => '或輸入復原碼',

                'validation_attribute' => '復原碼',

                'messages' => [

                    'invalid' => '你輸入的復原碼無效。',

                    'rate_limited' => '嘗試次數過多，請稍後再試。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '停用驗證器應用程式',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => '驗證器應用程式已停用',
        ],

    ],

];
