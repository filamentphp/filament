<?php

return [

    'title' => 'Verifica il tuo indirizzo email',

    'heading' => 'Verifica il tuo indirizzo email',

    'actions' => [

        'resend_notification' => [
            'label' => 'Invia nuovamente',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Non hai ricevuto la mail?',
        'notification_sent' => 'Abbiamo inviato una mail a :email contenente le istruzioni su come verificare il tuo indirizzo email.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Abbiamo inviato nuovamente la mail.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Troppi tentativi di invio',
            'body' => 'Riprova tra :seconds secondi.',
        ],

    ],

];
