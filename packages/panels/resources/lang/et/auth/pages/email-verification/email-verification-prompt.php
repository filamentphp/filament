<?php

return [

    'title' => 'Kinnitage oma e-posti aadress',

    'heading' => 'Kinnitage oma e-posti aadress',

    'actions' => [

        'resend_notification' => [
            'label' => 'Saada uuesti',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Kas te ei saanud meie saadetud e-kirja?',
        'notification_sent' => 'Saatsime e-kirja aadressile :email, mis sisaldab juhiseid teie e-posti aadressi kinnitamiseks.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Saatsime e-kirja uuesti.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Liiga palju uuesti saatmise katseid',
            'body' => 'Palun proovige uuesti :seconds sekundi pärast.',
        ],

    ],

];
