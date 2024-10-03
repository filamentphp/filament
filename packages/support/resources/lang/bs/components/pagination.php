<?php

return [

    'label' => 'Navigacija po stranicama',

    'overview' => '{1} Prikazuje 1 rezultat|[2,*]Prikazivanje :first od :last od ukupno :total rezultata',

    'fields' => [

        'records_per_page' => [

            'label' => 'po stranici',

            'options' => [
                'all' => 'Svi',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Idite na stranicu :page',
        ],

        'next' => [
            'label' => 'Dalje',
        ],

        'previous' => [
            'label' => 'Nazad',
        ],

    ],

];
