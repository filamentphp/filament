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

        'first' => [
            'label' => 'Pirmas',
        ],

        'go_to_page' => [
            'label' => 'Eiti į puslapį :page',
        ],

        'last' => [
            'label' => 'Paskutinis',
        ],

        'next' => [
            'label' => 'Kitas',
        ],

        'previous' => [
            'label' => 'Buvęs',
        ],

    ],

];
