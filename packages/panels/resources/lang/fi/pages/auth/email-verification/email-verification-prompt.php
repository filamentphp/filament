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
        'notification_sent' => 'Sähökpostiin :email on lähetetty viesti joka sisältää ohjeet sähköpostin vahvistamiseen.',
        'notification_resent' => 'Sähköposti on lähetetty uudelleen.',
        'notification_resend_throttled' => 'Liian monta lähetyksen yritystä. Yritä uudelleen :seconds sekunnin kuluttua.',
    ],

];
