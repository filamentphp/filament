<?php

return [

    'title' => 'パスワードリセット',

    'heading' => 'パスワードをリセット',

    'form' => [

        'email' => [
            'label' => 'メールアドレス',
        ],

        'password' => [
            'label' => 'パスワード',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'パスワードの確認',
        ],

        'actions' => [

            'reset' => [
                'label' => 'パスワードをリセット',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'リセットの試行回数が多すぎます',
            'body' => ':seconds 秒後に再試行してください。',
        ],

    ],

];
