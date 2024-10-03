<?php

return [

    'title' => 'Verifique su dirección de correo electrónico',

    'heading' => 'Verifique su dirección de correo electrónico',

    'actions' => [

        'resend_notification' => [
            'label' => 'Reenviar',
        ],

    ],

    'messages' => [
        'notification_not_received' => '¿No ha recibido el correo electrónico que enviamos?',
        'notification_sent' => 'Hemos enviado un correo electrónico a :email con instrucciones sobre cómo verificar su dirección de correo electrónico.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Hemos reenviado el correo electrónico.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Demasiados intentos de reenvío',
            'body' => 'Por favor, inténtelo de nuevo en :seconds segundos.',
        ],

    ],

];
