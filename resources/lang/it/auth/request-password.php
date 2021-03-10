<?php

return [

    'buttons' => [

        'submit' => [
            'label' => 'Invia link per reimpostare la password',
        ],

    ],

    'form' => [

        'email' => [
            'hint' => 'Torna all\'accesso',
            'label' => 'Indirizzo email',
        ],

    ],

    'messages' => [

        'throttled' => 'Troppi tentativi di accesso. Per favore riprova tra :seconds secondi.',

        'passwords' => [
            'sent' => 'Ti abbiamo inviato un\'email con il link per reimpostare la password!',
            'throttled' => 'Per favore attendi prima di riprovare.',
            'user' => 'Non siamo riusciti a trovare un utente con quell\'indirizzo email.',
        ],

    ],

    'title' => 'Reimposta password',

];
