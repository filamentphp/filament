<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => 'メール認証コード',

            'below_content' => 'ログイン時に本人確認を行うため、メールアドレスに一時的なコードを受け取ります。',

            'messages' => [
                'enabled' => '有効',
                'disabled' => '無効',
            ],

        ],

    ],

    'login_form' => [

        'label' => 'メールでコードを送信',

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

];
