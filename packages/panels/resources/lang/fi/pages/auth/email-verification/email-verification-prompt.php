<?php

return [

    'title' => 'Vahvista sähköpostiosoite',

    'heading' => 'Vahvista sähköpostiosoite',

    'actions' => [

        'resend_notification' => [
            'label' => 'Lähetä uudelleen ',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Etkö saanut lähettämäämme sähköpostia?',
        'notification_sent' => 'Sähköpostiin :email on lähetetty viesti joka sisältää ohjeet sähköpostiosoitteen vahvistamiseen.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Sähköposti on lähetetty uudelleen.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Liian monta lähetyksen yritystä',
            'body' => 'Yritä uudelleen :seconds sekunnin kuluttua.',
        ],

    ],

];
