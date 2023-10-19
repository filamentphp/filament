<?php

return [

    'title' => 'Verifiera din e-postadress',

    'heading' => 'Verifiera din e-postadress',

    'actions' => [

        'resend_notification' => [
            'label' => 'Skicka igen',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Inte fått e-postmeddelandet vi skickade?',
        'notification_sent' => 'Vi skickade ett meddelande till :email med instruktioner på hur du verifierar din e-postadress.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Vi skickade meddelandet igen.',
        ],

        'notification_resend_throttled' => [
            'title' => 'För många försök att skicka igen',
            'body' => 'Vänligen försök igen om :seconds sekunder.',
        ],

    ],

];
