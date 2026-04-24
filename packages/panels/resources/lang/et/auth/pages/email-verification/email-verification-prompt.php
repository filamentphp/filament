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
        'notification_not_received' => 'Ei ole saanud e-posti, mille me saatsime?',
        'notification_sent' => 'Oleme saatnud e-kirja aadressile :email, mis sisaldab juhiseid, kuidas kinnitada oma e-posti aadressi.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Oleme e-kirja uuesti saatnud.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Liiga palju uuesti saatmise katseid',
            'body' => 'Palun proovige uuesti :seconds sekundi pärast.',
        ],

    ],

];
