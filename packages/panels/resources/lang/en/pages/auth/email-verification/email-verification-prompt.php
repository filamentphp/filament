<?php

return [

    'title' => 'Verify your email address',

    'heading' => 'Verify your email address',

    'actions' => [

        'resend_notification' => [
            'label' => 'Resend it',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Not received the email we sent?',
        'notification_sent' => 'We\'ve sent an email to :email containing instructions on how to verify your email address.',
        'notification_resent' => 'We\'ve resent the email.',
        'notification_resend_throttled' => 'Too many resend attempts. Please try again in :seconds seconds.',
    ],

];
