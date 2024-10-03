<?php

return [

    'title' => 'Zweryfikuj swój adres e-mail',

    'heading' => 'Weryfikacja adresu e-mail',

    'actions' => [

        'resend_notification' => [
            'label' => 'Wyślij ponownie',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Nie otrzymałeś wysłanej przez nas wiadomości?',
        'notification_sent' => 'Na adres :email wysłaliśmy instrukcję weryfikacji twojego adresu e-mail.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Wysłaliśmy ponownie wiadomość e-mail.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Zbyt wiele prób ponownej wysyłki wiadomości',
            'body' => 'Spróbuj ponownie za :seconds sekund.',
        ],

    ],

];
