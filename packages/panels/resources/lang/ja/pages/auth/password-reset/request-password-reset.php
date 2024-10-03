<?php

return [

    'title' => 'パスワードリセット',

    'heading' => 'パスワードをお忘れですか？',

    'actions' => [

        'login' => [
            'label' => 'ログインへ戻る',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'メールアドレス',
        ],

        'actions' => [

            'request' => [
                'label' => 'メールを送信',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'リクエストが多すぎます',
            'body' => ':seconds 秒後に再試行してください。',
        ],

    ],

];
