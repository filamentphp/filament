<?php

return [

    'label' => '設定',

    'modal' => [

        'heading' => '設定驗證器應用程式',

        'description' => <<<'BLADE'
            請使用驗證器應用程式完成設定，例如 Google Authenticator（<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>、<x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>）。
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => '使用你的驗證器應用程式掃描此 QR 碼：',

                'alt' => '使用驗證器應用程式掃描的 QR 碼',

            ],

            'text_code' => [

                'instruction' => '或手動輸入此代碼：',

                'messages' => [
                    'copied' => '已複製',
                ],

            ],

            'recovery_codes' => [

                'instruction' => '請將以下復原碼妥善保存在安全的地方。復原碼只會顯示一次，如無法使用驗證器應用程式，你便需要使用這些復原碼：',

            ],

        ],

        'form' => [

            'password' => [
                'label' => '現有密碼',
                'validation_attribute' => '現有密碼',
            ],

            'code' => [

                'label' => '輸入驗證器應用程式顯示的 6 位數字驗證碼',

                'validation_attribute' => '驗證碼',

                'below_content' => '每次登入或執行敏感操作時，你都須輸入驗證器應用程式顯示的 6 位數字驗證碼。',

                'messages' => [

                    'invalid' => '你輸入的驗證碼無效。',

                    'rate_limited' => '嘗試次數過多，請稍後再試。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '啟用驗證器應用程式',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => '驗證器應用程式已啟用',
        ],

    ],

];
