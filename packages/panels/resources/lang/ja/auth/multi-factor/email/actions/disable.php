<?php

return [

    'label' => '無効化',

    'modal' => [

        'heading' => 'メール認証コードを無効化',

        'description' => 'メール認証コードの受信を停止してもよろしいですか？この機能を無効にすると、アカウントのセキュリティが一段階低下します。',

        'form' => [

            'code' => [

                'label' => 'メールで送信された6桁のコードを入力',

                'validation_attribute' => 'コード',

                'actions' => [

                    'resend' => [

                        'label' => '新しいコードをメールで送信',

                        'notifications' => [

                            'resent' => [
                                'title' => '新しいコードをメールで送信しました',
                            ],

                            'throttled' => [
                                'title' => '再送信の試行が多すぎます。しばらくしてから再度お試しください。',
                            ],

                        ],

                    ],

                ],

                'messages' => [

                    'invalid' => '入力されたコードが無効です。',

                ],

            ],

        ],

        'actions' => [

            'submit' => [
                'label' => 'メール認証コードを無効化',
            ],

        ],

    ],

    'notifications' => [

        'disabled' => [
            'title' => 'メール認証コードを無効化しました',
        ],

    ],

];
