<?php

return [

    'title' => 'אמת את כתובת הדוא"ל שלך',

    'heading' => 'אמת את כתובת הדוא"ל שלך',

    'actions' => [

        'resend_notification' => [
            'label' => 'שלח שוב',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'לא קבלת את הדוא"ל ששלחנו?',
        'notification_sent' => 'שלחנו דואר אלקטרוני ל-:email המכיל הוראות כיצד לאמת את כתובת הדוא"ל שלך.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'שלחנו שוב את הדוא"ל.',
        ],

        'notification_resend_throttled' => [
            'title' => 'יותר מדי נסיונות של שליחה מחדש',
            'body' => 'אנא נסה שוב בעוד  :seconds שניות.',
        ],

    ],

];
