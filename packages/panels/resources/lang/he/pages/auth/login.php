<?php

return [

    'title' => 'התחברות',

    'heading' => 'התחבר לחשבון שלך',

    'actions' => [

        'register' => [
            'before' => 'או',
            'label' => 'הירשם לחשבון',
        ],

        'request_password_reset' => [
            'label' => 'שכחת את הסיסמה שלך?',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'כתובת דואר אלקטרוני',
        ],

        'password' => [
            'label' => 'סיסמה',
        ],

        'remember' => [
            'label' => 'זכור אותי',
        ],

        'actions' => [

            'authenticate' => [
                'label' => 'התחבר',
            ],

        ],

    ],

    'messages' => [

        'failed' => 'הפרטים שהזנת שגויים או לא קיימים.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'יותר מידי ניסיונות התחברות. אנא נסה שוב מאוחר יותר.',
            'body' => 'נסה שוב בעוד :seconds שניות.',
        ],

    ],

];
