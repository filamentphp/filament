<?php

return [

    'title' => 'メールアドレスの確認',

    'heading' => 'メールアドレスの確認',

    'actions' => [

        'resend_notification' => [
            'label' => '再送する',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'お送りしたメールが届いていませんか？',
        'notification_sent' => 'メールアドレスの確認方法を記載したメールを :email へ送信しました。',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'メールを再送しました。',
        ],

        'notification_resend_throttled' => [
            'title' => '再送が多すぎます',
            'body' => ':seconds 秒後に再試行してください。',
        ],

    ],

];
