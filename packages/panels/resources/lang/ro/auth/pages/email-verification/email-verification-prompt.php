<?php

return [

    'title' => 'Confirmă adresa de email',

    'heading' => 'Confirmă adresa de email',

    'actions' => [

        'resend_notification' => [
            'label' => 'Retrimite',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Nu ai primit emailul de verificare?',
        'notification_sent' => 'S-a trimis un email la :email cu instrucțiuni pentru a confirma adresa de email.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Am retrimis emailul.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Prea multe încercări consecutive de retrimitere',
            'body' => 'Încearcă te rog din nou peste :seconds secunde.',
        ],

    ],

];
