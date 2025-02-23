<?php

return [

    'title' => 'Vérifier votre adresse email',

    'heading' => 'Vérifier votre adresse email',

    'actions' => [

        'resend_notification' => [
            'label' => 'Renvoyer',
        ],

    ],

    'messages' => [
        'notification_not_received' => "Vous n'avez pas reçu l'email envoyé ?",
        'notification_sent' => "Nous vous avons envoyé un email à l'adresse :email contenant les informations pour vérifier votre adresse email.",
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Nous avons renvoyé un email',
        ],

        'notification_resend_throttled' => [
            'title' => 'Trop de tentatives de renvoi',
            'body' => 'Merci de réessayer dans :seconds secondes.',
        ],

    ],

];
