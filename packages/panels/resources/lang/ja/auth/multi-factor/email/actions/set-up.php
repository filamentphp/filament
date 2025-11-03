<?php

return [

    'label' => '設定',

    'modal' => [

        'heading' => 'メール認証コードの設定',

        'description' => 'サインイン時や機密性の高い操作を行う際に、メールで送信される6桁の認証コードを入力する必要があります。セットアップを完了するには、メールに記載された6桁のコードを確認してください。',

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
                'label' => 'メール認証コードを有効化',
            ],

        ],

    ],

    'notifications' => [

        'enabled' => [
            'title' => 'メール認証コードを有効化しました',
        ],

    ],

];
