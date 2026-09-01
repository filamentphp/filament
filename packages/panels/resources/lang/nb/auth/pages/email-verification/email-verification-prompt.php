<?php

return [

    'title' => 'Bekreft din e-postadresse',

    'heading' => 'Bekreft din e-postadresse',

    'actions' => [

        'resend_notification' => [
            'label' => 'Send på nytt',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Ikke mottatt e-posten vi sendte?',
        'notification_sent' => 'Vi har sendt e-post til :email med informasjon om hvordan du bekrefter din e-postadresse.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Vi har sendt e-post på nytt.',
        ],

        'notification_resend_throttled' => [
            'title' => 'For mange forsøk på nye sendinger',
            'body' => 'Vennligst prøv igjen om :seconds sekunder.',
        ],

    ],

];
