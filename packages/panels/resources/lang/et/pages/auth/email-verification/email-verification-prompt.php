<?php

return [

    'title' => 'Kinnita oma e-posti aadress',

    'heading' => 'Kinnita oma e-posti aadress',

    'actions' => [

        'resend_notification' => [
            'label' => 'Saada uuesti',
        ],

    ],

    'messages' => [
        'notification_not_received' => [
            'label' => 'Ei saanud e-kirja?',
        ],

        'notification_sent' => [
            'label' => 'Saatsime kinnituslingi sinu e-posti aadressile.',
        ],
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Saatsime e-kirja uuesti.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Liiga palju saatmise katseid',
            'body' => 'Palun proovi uuesti :seconds sekundi pärast.',
        ],

    ],

];
