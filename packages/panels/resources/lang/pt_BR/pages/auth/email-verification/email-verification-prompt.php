<?php

return [

    'title' => 'Verifique seu e-mail',

    'heading' => 'Verifique seu e-mail',

    'actions' => [

        'resend_notification' => [
            'label' => 'Reenviar',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Não recebeu o e-mail que enviamos?',
        'notification_sent' => 'Enviamos um e-mail para :email contendo instruções sobre como verificar seu e-mail.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Reenviamos o e-mail.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Muitas tentativas de reenvio',
            'body' => 'Por favor tente novamente em :seconds segundos.',
        ],

    ],

];
