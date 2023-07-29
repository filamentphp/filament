<?php

return [

    'title' => 'Verifikasi alamat email Anda',

    'heading' => 'Verifikasi alamat email Anda',

    'actions' => [

        'resend_notification' => [
            'label' => 'Resend it',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Belum menerima email?',
        'notification_sent' => 'Kami telah mengirimkan email ke :email yang berisikan instruksi cara verifikasi alamat email Anda.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Email telah dikirim ulang.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Terlalu banyak permintaan. Silakan coba lagi dalam :seconds detik.',
        ],

    ],

];
