<?php

return [

    'title' => 'Apstipriniet savu e-pasta adresi',

    'heading' => 'Apstipriniet savu e-pasta adresi',

    'actions' => [

        'resend_notification' => [
            'label' => 'Nosūtīt vēlreiz',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Nesaņēmāt mūsu nosūtīto e-pastu?',
        'notification_sent' => 'Uz adresi :email tika nosūtīts e-pasts ar norādēm, kā apstiprināt savu e-pasta adresi.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'E-pasts tika nosūtīts atkārtoti.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Pārāk daudz mēģinājumu',
            'body' => 'Lūdzu, mēģiniet vēlreiz pēc :seconds sekundēm.',
        ],

    ],

];
