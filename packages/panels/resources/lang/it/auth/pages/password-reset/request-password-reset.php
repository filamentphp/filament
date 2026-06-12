<?php

return [

    'title' => 'Reimposta la tua password',

    'heading' => 'Hai smarrito la password?',

    'actions' => [

        'login' => [
            'label' => 'torna al login',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Email',
        ],

        'actions' => [

            'request' => [
                'label' => 'Invia email',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Se il tuo account non esiste, non riceverai l\'email.',
        ],

        'throttled' => [
            'title' => 'Troppe richieste',
            'body' => 'Riprova tra :seconds secondi.',
        ],

    ],

];
