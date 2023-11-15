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

        'go_to_page' => [
            'label' => 'Idi na stranicu :page',
        ],

        'next' => [
            'label' => 'Naprijed',
        ],

        'previous' => [
            'label' => 'Natrag',
        ],

    ],

];
