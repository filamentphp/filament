<?php

return [

    'title' => 'E-mailadres verifiëren',

    'heading' => 'E-mailadres verifiëren',

    'actions' => [

        'resend_notification' => [
            'label' => 'Opnieuw verzenden',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Geen e-mail ontvangen?',
        'notification_sent' => 'We hebben een e-mail gestuurd naar :email met instructies om je e-mailadres te verifiëren.',

    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'E-mail opnieuw verzonden.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Te veel verzendpogingen',
            'body' => 'Probeer het opnieuw over :seconds seconden.',
        ],

    ],

];
