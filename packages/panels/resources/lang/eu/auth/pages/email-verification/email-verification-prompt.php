<?php

return [

    'title' => 'Egiaztatu zure helbide elektronikoa',

    'heading' => 'Egiaztatu zure helbide elektronikoa',

    'actions' => [

        'resend_notification' => [
            'label' => 'Birbidali',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Ez duzu bidali dugun mezu elektronikoa jaso?',
        'notification_sent' => ':email helbidera mezu elektroniko bat bidali dugu zure helbide elektronikoa egiaztatzeko argibideekin.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Mezu elektronikoa birbidali dugu.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Birbidalketa-saiakera gehiegi',
            'body' => 'Mesedez, saiatu berriro :seconds segundu barru.',
        ],

    ],

];
