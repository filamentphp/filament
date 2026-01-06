<?php

return [

    'label' => '設定',

    'modal' => [

        'heading' => '設定驗證器應用程式',

        'description' => <<<'BLADE'
            您需要像 Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android</x-filament::link>) 這樣的應用程式來完成此過程。
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => '使用您的驗證器應用程式掃描此 QR 碼：',

                'alt' => '使用驗證器應用程式掃描的 QR 碼',

            ],

            'text_code' => [

                'instruction' => '或手動輸入此代碼：',

                'messages' => [
                    'copied' => '已複製',
                ],

            ],

            'recovery_codes' => [

                'instruction' => '請將以下復原碼保存在安全的地方。它們只會顯示一次，但如果您無法存取驗證器應用程式，您將需要它們：',

            ],

        ],

        'form' => [

            'code' => [

                'label' => '輸入來自驗證器應用程式的 6 位數驗證碼',

                'validation_attribute' => '驗證碼',

                'below_content' => '您每次登入或執行敏感操作時，都需要輸入來自驗證器應用程式的 6 位數驗證碼。',

                'messages' => [

                    'invalid' => '您輸入的驗證碼無效。',

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
