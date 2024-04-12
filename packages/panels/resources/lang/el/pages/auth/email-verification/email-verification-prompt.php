<?php

return [

    'title' => 'Επαληθεύστε τη διεύθυνση ηλεκτρονικού ταχυδρομείου σας',

    'heading' => 'Επαληθεύστε τη διεύθυνση ηλεκτρονικού ταχυδρομείου σας',

    'actions' => [

        'resend_notification' => [
            'label' => 'Επαναποστολή email επαλήθευσης',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Not received the email we sent?',
        'notification_sent' => 'We\'ve sent an email to :email containing instructions on how to verify your email address.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'We\'ve resent the email.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Πάρα πολλά αιτήματα επαναποστολής',
            'body' => 'Παρακαλούμε δοκιμάστε πάλι σε :seconds δευτερόλεπτα.',
        ],

    ],

];
