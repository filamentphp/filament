<?php

return [

    'title' => 'Potvrdite svoju adresu e-pošte',

    'heading' => 'Potvrdite svoju adresu e-pošte',

    'actions' => [

        'resend_notification' => [
            'label' => 'Ponovo pošalji',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Niste primili e-poštu koju smo poslali?',
        'notification_sent' => 'Poslali smo e-poštu na :email sa uputstvom o tome kako potvrditi svoju adresu e-pošte.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Ponovo smo poslali e-poštu.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Previše pokušaja ponovnog slanja',
            'body' => 'Molim vas, pokušajte ponovno za :seconds sekundi.',
        ],

    ],

];
