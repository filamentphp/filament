<?php

return [

    'title' => 'Εγγραφή',

    'heading' => 'Εγγραφή',

    'actions' => [

        'login' => [
            'before' => 'ή',
            'label' => 'συνδεθείτε στο λογαριασμό σας',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Διεύθυνση Email',
        ],

        'name' => [
            'label' => 'Όνομα',
        ],

        'password' => [
            'label' => 'Κωδικός',
            'validation_attribute' => 'Κωδικός',
        ],

        'password_confirmation' => [
            'label' => 'Επιβεβαίωση Κωδικού',
        ],

        'actions' => [

            'register' => [
                'label' => 'Εγγραφείτε',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Πάρα πολλά αιτήματα εγγραφής',
            'body' => 'Παρακαλούμε δοκιμάστε πάλι σε :seconds δευτερόλεπτα.',
        ],

    ],

];
