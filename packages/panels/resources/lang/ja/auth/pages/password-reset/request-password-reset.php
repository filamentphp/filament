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

        'sent' => [
            'body' => 'アカウントが存在しない場合、メールは届きません。',
        ],

        'throttled' => [
            'title' => 'リクエストが多すぎます',
            'body' => ':seconds 秒後に再試行してください。',
        ],

    ],

];
