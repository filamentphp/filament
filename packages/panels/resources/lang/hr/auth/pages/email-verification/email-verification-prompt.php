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
        'notification_not_received' => 'Nisi primio e-poštu koju smo poslali?',
        'notification_sent' => 'Poslali smo e-poštu na :email s uputama o tome kako potvrditi svoju adresu e-pošte.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Ponovno smo poslali e-poštu.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Previše pokušaja ponovnog slanja',
            'body' => 'Molim te, pokušaj ponovno za :seconds sekundi.',
        ],

    ],

];
