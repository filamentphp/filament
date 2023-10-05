<?php

return [

    'title' => 'アカウント登録',

    'heading' => 'アカウントを登録',

    'actions' => [

        'login' => [
            'before' => 'または',
            'label' => 'アカウントにログイン',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'メールアドレス',
        ],

        'name' => [
            'label' => '名前',
        ],

        'password' => [
            'label' => 'パスワード',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'パスワードの確認',
        ],

        'actions' => [

            'register' => [
                'label' => '登録',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => '登録の試行回数が多すぎます',
            'body' => ':seconds 秒後に再試行してください。',
        ],

    ],

];
