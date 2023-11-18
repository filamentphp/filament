<?php

return [

    'title' => 'Potvrdite svoju email adresu',

    'heading' => 'Potvrdite svoju email adresu',

    'actions' => [

        'resend_notification' => [
            'label' => 'Ponovo pošalji',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Niste primili email koji smo poslali?',
        'notification_sent' => 'Poslali smo email na :email s uputama o tome kako potvrditi svoju email adresu.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Ponovno smo poslali email.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Previše pokušaja ponovnog slanja',
            'body' => 'Molim pokušajte ponovno za :seconds sekundi.',
        ],

    ],

];
