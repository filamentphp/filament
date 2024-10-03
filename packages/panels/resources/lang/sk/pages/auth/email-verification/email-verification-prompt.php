<?php

return [

    'title' => 'Potvrďte svoju emailovú adresu',

    'heading' => 'Potvrďte svoju emailovú adresu',

    'actions' => [

        'resend_notification' => [
            'label' => 'Odoslať znovu',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Nedostali ste email?',
        'notification_sent' => 'Poslali sme email na adresu :email s inštrukciami na overenie emailovej adresy.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Znovu sme odoslali email.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Príliš veľa pokusov',
            'body' => 'Vyskúšajte to znovu o :seconds sekúnd.',
        ],

    ],

];
