<?php

return [

    'title' => 'Verifique o seu endereço de e-mail',

    'heading' => 'Verifique o seu endereço de e-mail',

    'actions' => [

        'resend_notification' => [
            'label' => 'Reenviar',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Não recebeu o e-mail que enviámos?',
        'notification_sent' => 'Enviámos um e-mail para :email com as instruções sobre como verificar o seu endereço de e-mail.',
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
