<?php

return [

    'title' => 'ログイン',

    'heading' => 'アカウントにログイン',

    'actions' => [

        'register' => [
            'before' => 'または',
            'label' => 'アカウントを登録',
        ],

        'request_password_reset' => [
            'label' => 'パスワードをお忘れですか？',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'メールアドレス',
        ],

        'password' => [
            'label' => 'パスワード',
        ],

        'remember' => [
            'label' => 'ログインしたままにする',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'ログイン',
            ],

        ],

    ],

    'messages' => [

        'failed' => '認証に失敗しました。',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'ログインの試行回数が多すぎます',
            'body' => ':seconds 秒後に再試行してください。',
        ],

    ],

];
