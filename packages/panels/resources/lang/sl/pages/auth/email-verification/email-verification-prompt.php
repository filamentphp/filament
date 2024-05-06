<?php

return [

    'title' => 'Potrdite svoj e-poštni naslov',

    'heading' => 'Potrdite svoj e-poštni naslov',

    'actions' => [

        'resend_notification' => [
            'label' => 'Ponovno pošlji',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Niste prejeli e-pošte, ki smo vam jo poslali?',
        'notification_sent' => 'Poslali smo e-poštno sporočilo na :email z navodili, kako potrditi svoj e-poštni naslov.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Ponovno smo poslali e-poštno sporočilo.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Preveč poskusov ponovnega pošiljanja',
            'body' => 'Poskusite znova čez :seconds sekund.',
        ],

    ],

];
