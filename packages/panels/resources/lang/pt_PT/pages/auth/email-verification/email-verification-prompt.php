<?php

return [

    'title' => 'Verifique o seu e-mail',

    'heading' => 'Verifique o seu e-mail',

    'actions' => [

        'resend_notification' => [
            'label' => 'Reenviar',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Não recebeu o e-mail que enviamos?',
        'notification_sent' => 'Enviamos um e-mail para :email com instruções sobre como verificar o seu e-mail.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'E-mail reenviado.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Muitas tentativas de reenvio',
            'body' => 'Por favor, tente novamente em :seconds segundos.',
        ],

    ],

];
