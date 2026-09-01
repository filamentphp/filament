<?php

return [

    'label' => 'Navigacija stranicama',

    'overview' => '{1} Prikazuje se 1 rezultat|[2,*] Prikazuje se :first do :last od ukupno :total rezultata',

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
            'label' => 'Zadnja',
        ],

        'next' => [
            'label' => 'Naprijed',
        ],

        'previous' => [
            'label' => 'Natrag',
        ],

    ],

];
