<?php

return [

    'title' => 'Verifikacija adrese vaše e-pošte',

    'heading' => 'Verifikacija adrese vaše e-pošte',

    'actions' => [

        'resend_notification' => [
            'label' => 'Ponovo pošalji',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Još uvek niste primili e-poštu?',
        'notification_sent' => 'Poslali smo poruku na :email sa instrukcijama za verifikaciju adrese vaše e-pošte.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Ponovo smo poslali poruku.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Previše ponovnih slanja',
            'body' => 'Pokušajte ponovo za :seconds s.',
        ],

    ],

];
