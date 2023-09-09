<?php

return [

    'title' => 'Mengesahkan alamat e-mel anda',

    'heading' => 'Mengesahkan alamat e-mel anda',

    'actions' => [

        'resend_notification' => [
            'label' => 'Hantar semula',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Tidak menerima e-mel yang kami hantar?',
        'notification_sent' => 'Kami telah menghantar e-mel kepada :email yang mengandungi arahan tentang cara untuk mengesahkan alamat e-mel anda.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Kami telah menghantar e-mel.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Terlalu banyak percubaan menghantar semula',
            'body' => 'Sila cuba lagi dalam :second saat.',
        ],

    ],

];
