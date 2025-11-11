<?php

return [

    'label' => '設定',

    'modal' => [

        'heading' => '認証アプリの設定',

        'description' => <<<'BLADE'
            この設定を完了するには、Google Authenticator (<x-filament::link href="https://itunes.apple.com/us/app/google-authenticator/id388497605" target="_blank">iOS版</x-filament::link>, <x-filament::link href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank">Android版</x-filament::link>) などの認証アプリが必要です。
            BLADE,

        'content' => [

            'qr_code' => [

                'instruction' => '認証アプリで次のQRコードをスキャンしてください：',

                'alt' => '認証アプリでスキャンするためのQRコード',

            ],

            'text_code' => [

                'instruction' => 'または、次のコードを手動で入力してください：',

                'messages' => [
                    'copied' => 'コピーしました',
                ],

            ],

            'recovery_codes' => [

                'instruction' => '以下のリカバリーコードを安全な場所に保存してください。  
                    これらは今回のみ表示されますが、認証アプリにアクセスできなくなった場合に必要になります。',

            ],

        ],

        'form' => [

            'code' => [

                'label' => '認証アプリで生成された6桁のコードを入力',

                'validation_attribute' => 'コード',

                'below_content' => 'サインイン時や機密操作を行う際には、認証アプリで生成された6桁のコードを入力する必要があります。',

                'messages' => [

                    'invalid' => '入力されたコードが無効です。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '認証アプリを有効化',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => '認証アプリを有効化しました',
        ],

    ],

];
