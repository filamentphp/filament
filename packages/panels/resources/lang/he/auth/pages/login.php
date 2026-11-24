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

    'multi_factor' => [

        'heading' => 'אמת את זהותך',

        'subheading' => 'כדי להמשיך בהתחברות, עליך לאמת את זהותך.',

        'form' => [

            'provider' => [
                'label' => 'כיצד תרצה לאמת את זהותך?',
            ],

            'actions' => [

                'authenticate' => [
                    'label' => 'אשר התחברות',
                ],

            ],

        ],

    ],

    'messages' => [

        'failed' => 'הפרטים שהזנת שגויים או לא קיימים.',

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'יותר מדי ניסיונות התחברות',
            'body' => 'אנא נסה שוב בעוד :seconds שניות.',
        ],

    ],

];
