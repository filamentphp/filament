<?php

return [

    'label' => 'プロフィール',

    'form' => [

        'email' => [
            'label' => 'メールアドレス',
        ],

        'name' => [
            'label' => '名前',
        ],

        'password' => [
            'label' => '新しいパスワード',
            'validation_attribute' => 'パスワード',
        ],

        'password_confirmation' => [
            'label' => '新しいパスワードの確認',
            'validation_attribute' => 'パスワード確認',
        ],

        'current_password' => [
            'label' => '現在のパスワード',
            'below_content' => 'セキュリティのため、続行するにはパスワードを確認してください。',
            'validation_attribute' => '現在のパスワード',
        ],

        'actions' => [

            'save' => [
                'label' => '変更を保存',
            ],

        ],

    ],

    'multi_factor_authentication' => [
        'label' => '二要素認証 (2FA)',
    ],

    'notifications' => [

        'email_change_verification_sent' => [
            'title' => 'メールアドレス変更リクエストを送信しました',
            'body' => 'メールアドレスの変更リクエストが :email に送信されました。変更を確認するためにメールをご確認ください。',
        ],

        'saved' => [
            'title' => '保存しました',
        ],

    ],

    'actions' => [

        'cancel' => [
            'label' => 'キャンセル',
        ],

    ],

];
