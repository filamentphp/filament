<?php

return [
    'title' => 'I-verify ang email address mo',
    'heading' => 'I-verify ang email address mo',
    'actions' => [
        'resend_notification' => [
            'label' => 'Ipadala ulit',
        ],
    ],
    'messages' => [
        'notification_not_received' => 'Hindi mo natanggap ang email na ipinadala namin?',
        'notification_sent' => 'Nagpadala kami ng email sa :email na may mga tagubilin kung paano i-verify ang email address mo.',
    ],
    'notifications' => [
        'notification_resent' => [
            'title' => 'Ipinadala ulit namin ang email.',
        ],
        'notification_resend_throttled' => [
            'title' => 'Masyadong maraming resend attempt',
            'body' => 'Subukan ulit pagkalipas ng :seconds segundo.',
        ],
    ],
];
