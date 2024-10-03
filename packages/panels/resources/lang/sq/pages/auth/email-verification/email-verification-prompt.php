<?php

return [

    'title' => 'Verifikoni adresën tuaj të emailit',

    'heading' => 'Verifikoni adresën tuaj të emailit',

    'actions' => [

        'resend_notification' => [
            'label' => 'Ridërgojeni',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'Nuk e morët emailin që dërguam?',
        'notification_sent' => 'Ne kemi dërguar një email në :email që përmban udhëzime se si të verifikoni adresën tuaj të emailit.',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'Ne e kemi ridërguar emailin.',
        ],

        'notification_resend_throttled' => [
            'title' => 'Shumë përpjekje për ridërgim',
            'body' => 'Ju lutemi provoni përsëri në :seconds sekonda.',
        ],

    ],

];
