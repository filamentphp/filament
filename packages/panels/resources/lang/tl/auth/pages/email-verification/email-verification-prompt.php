<?php

return [

    'title' => 'I-verify ang iyong email address',

    'heading' => 'I-verify ang iyong email address',

    'actions' => [

        'resend_notification' => [
            'label' => 'Ipadala ulit',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Hindi mo natanggap ang email na ipinadala namin?',
        'notification_sent' => 'Nagpadala kami ng email sa :email na may instructions kung paano i-verify ang iyong email address.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Naipadala ulit namin ang email.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Masyadong maraming subok na magpadala ulit',
            'body' => 'Subukan ulit pagkalipas ng :seconds segundo.',
        ],

    ],

];
