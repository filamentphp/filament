<?php

return [

    'title' => 'Verifica la teva adreça de correu electrònic',

    'heading' => 'Verifica la teva adreça de correu electrònic',

    'actions' => [

        'resend_notification' => [
            'label' => 'Reenvia la notificació',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'No has rebut el correu electrònic?',
        'notification_sent' => 'Hem enviat un correu electrònic a :email amb instruccions sobre com verificar la teva adreça de correu electrònic.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Hem reenviat el correu electrònic.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Massa intents de reenviament',
            'body' => 'Si us plau, torna a intentar-ho en :seconds segons.',
        ],

    ],

];
