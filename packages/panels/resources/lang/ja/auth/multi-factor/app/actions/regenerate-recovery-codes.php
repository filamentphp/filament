<?php

return [

    'label' => 'リカバリーコードを再生成',

    'modal' => [

        'heading' => '認証アプリのリカバリーコードを再生成',

        'description' => 'リカバリーコードを紛失した場合は、ここで再生成できます。古いリカバリーコードはすぐに無効になります。',

        'form' => [

            'code' => [

                'label' => '認証アプリの6桁のコードを入力',

                'validation_attribute' => 'コード',

                'messages' => [

                    'invalid' => '入力されたコードが無効です。',

                ],

            ],

            'password' => [

                'label' => 'または、現在のパスワードを入力',

                'validation_attribute' => 'パスワード',

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'リカバリーコードを再生成',
            ],

        ],

    ],

    'notifications' => [

        'regenerated' => [
            'title' => '新しいリカバリーコードを生成しました',
        ],

    ],

    'show_new_recovery_codes' => [

        'modal' => [

            'heading' => '新しいリカバリーコード',

            'description' => '以下のリカバリーコードを安全な場所に保存してください。これらは一度しか表示されませんが、認証アプリにアクセスできなくなった場合に必要となります。',

            'actions' => [

                'submit' => [
                    'label' => '閉じる',
                ],

            ],

        ],

    ],

];
