<?php

return [

    'title' => 'Επαναφορά κωδικού',

    'heading' => 'Επαναφορά κωδικού',

    'form' => [

        'email' => [
            'label' => 'Διεύθυνση Email',
        ],

        'password' => [
            'label' => 'Κωδικός',
            'validation_attribute' => 'κωδικός',
        ],

        'password_confirmation' => [
            'label' => 'Επιβεβαίωση Κωδικού',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Επαναφορά Κωδικού',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Πάρα πολλά αιτήματα επαναφοράς',
            'body' => 'Παρακαλούμε δοκιμάστε πάλι σε :seconds δευτερόλεπτα.',
        ],

    ],

];
