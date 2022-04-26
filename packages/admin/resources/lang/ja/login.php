<?php

return [

    'title' => 'ログイン',

    'heading' => 'アカウントにログインする',

    'buttons' => [

        'submit' => [
            'label' => 'ログイン',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'メールアドレス',
        ],

        'password' => [
            'label' => 'パスワード',
        ],

        'remember' => [
            'label' => 'ログイン状態を保持する',
        ],

    ],

    'messages' => [
        'failed' => '認証に失敗しました。',
        'throttled' => 'ログインの試行回数が多すぎます。:seconds 秒後にお試しください。',
    ],

];
