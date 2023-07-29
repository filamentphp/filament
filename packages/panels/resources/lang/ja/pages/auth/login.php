<?php

return [

    'title' => 'ログイン',

    'heading' => 'アカウントにログインする',

    'form' => [

        'email' => [
            'label' => 'メールアドレス',
        ],

        'password' => [
            'label' => 'パスワード',
        ],

        'remember' => [
            'label' => 'ログイン状態を保持する',
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
            'title' => 'ログインの試行回数が多すぎます。:seconds 秒後にお試しください。',
        ],

    ],

];
