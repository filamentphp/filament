<?php

return [

    'label' => 'Lehekülgede navigatsioon',

    'overview' => '{1} Näitan 1 tulemust|[2,*] Näitan :first kuni :last :total tulemusest',

    'fields' => [

        'records_per_page' => [

            'label' => 'Lehel',

            'options' => [
                'all' => 'Kõik',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Esimene',
        ],

        'go_to_page' => [
            'label' => 'Mine lehele :page',
        ],

        'last' => [
            'label' => 'Viimane',
        ],

        'next' => [
            'label' => 'Järgmine',
        ],

        'previous' => [
            'label' => 'Eelmine',
        ],

    ],

];
