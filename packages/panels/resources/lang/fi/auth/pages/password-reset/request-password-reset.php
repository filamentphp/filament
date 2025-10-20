<?php

return [

    'title' => 'Salasana hukassa?',

    'heading' => 'Salasana hukassa?',

    'actions' => [

        'login' => [
            'label' => 'takaisin kirjautumiseen',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'Sähköpostiosoite',
        ],

        'actions' => [

            'request' => [
                'label' => 'Lähetä sähköposti',
            ],

        ],

    ],

    'notifications' => [

        'sent' => [
            'body' => 'Jos tiliä ei ole olemassa, et vastaanota sähköpostia.',
        ],

        'throttled' => [
            'title' => 'Liian monta pyyntöä',
            'body' => 'Yritä uudelleen :seconds sekunnin kuluttua.',
        ],

    ],

];
