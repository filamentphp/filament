<?php

return [

    'label' => 'Puslapiavimo navigacija',

    'overview' => 'Rodomi nuo :first iki :last rezultatai iš :total',

    'fields' => [

        'records_per_page' => [

            'label' => 'puslapyje',

            'options' => [
                'all' => 'Viską',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Eiti į puslapį :page',
        ],

        'next' => [
            'label' => 'Kitas',
        ],

        'previous' => [
            'label' => 'Buvęs',
        ],

    ],

];
