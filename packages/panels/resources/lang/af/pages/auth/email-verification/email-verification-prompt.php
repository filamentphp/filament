<?php

return [

    'title' => 'Verifieer jou e-posadres',

    'heading' => 'Verifieer jou e-posadres',

    'actions' => [

        'resend_notification' => [
            'label' => 'Stuur dit weer',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Nie die e-pos ontvang wat ons gestuur het nie?',
        'notification_sent' => 'Ons het \'n e-pos na :email gestuur wat instruksies bevat oor hoe om jou e-posadres te verifieer.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Ons het die e-pos weer gestuur.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Te veel herstuurpogings',
            'body' => 'Probeer asseblief weer oor :seconds sekondes.',
        ],

    ],

];
