<?php

return [

    'label' => '無効化',

    'modal' => [

        'heading' => '認証アプリを無効化',

        'description' => '本当に認証アプリの使用を停止しますか？この操作を行うと、アカウントのセキュリティが低下します。',

        'form' => [

            'code' => [

                'label' => '認証アプリに表示されている6桁のコードを入力してください',

                'validation_attribute' => 'コード',

                'actions' => [

                    'use_recovery_code' => [
                        'label' => '代わりにリカバリーコードを使用する',
                    ],

                ],

                'messages' => [

                    'invalid' => '入力されたコードが正しくありません。',
                ],

            ],

            'recovery_code' => [

                'label' => 'または、リカバリーコードを入力',

                'validation_attribute' => 'リカバリーコード',

                'messages' => [

                    'invalid' => '入力されたリカバリーコードが正しくありません。',
                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => '認証アプリを無効化',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => '認証アプリを無効化しました',
        ],

    ],

];
