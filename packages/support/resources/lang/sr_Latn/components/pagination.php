<?php

return [

    'label' => 'Navigacija stranicama',

    'overview' => '{1} Prikazan je 1 rezultat|[2,*] Prikazano je :first do :last od ukupno :total rezultata',

    'fields' => [

        'records_per_page' => [

            'label' => 'Po stranici',

            'options' => [
                'all' => 'Sve',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Prva',
        ],

        'go_to_page' => [
            'label' => 'Idi na stranicu :page',
        ],

        'last' => [
            'label' => 'Poslednja',
        ],

        'next' => [
            'label' => 'Napred',
        ],

        'previous' => [
            'label' => 'Nazad',
        ],

    ],

];
