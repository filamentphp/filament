<?php

return [

    'management_schema' => [

        'actions' => [

            'label' => '認証アプリ',

            'below_content' => '安全な認証アプリを使用して、ログイン確認用の一時コードを生成します。',

            'messages' => [
                'enabled' => '有効',
                'disabled' => '無効',
            ],

        ],

    ],

    'login_form' => [

        'label' => '認証アプリのコードを使用',

        'code' => [

            'label' => '認証アプリで生成された6桁のコードを入力',

            'validation_attribute' => 'コード',

            'actions' => [

                'use_recovery_code' => [
                    'label' => '代わりにリカバリーコードを使用',
                ],

            ],

            'messages' => [

                'invalid' => '入力されたコードが無効です。',

            ],

        ],

        'recovery_code' => [

            'label' => 'または、リカバリーコードを入力',

            'validation_attribute' => 'リカバリーコード',

            'messages' => [

                'invalid' => '入力されたリカバリーコードが無効です。',

            ],

        ],

    ],

];
