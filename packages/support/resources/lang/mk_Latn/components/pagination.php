<?php

return [

    'label' => 'Navigacija po stranici',

    'overview' => '{1} Prikažano 1 rezultat|[2,*] Prikažano :first do :last od :total rezultati',

    'fields' => [

        'records_per_page' => [

            'label' => 'Po stranica',

            'options' => [
                'all' => 'Site',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Prva',
        ],

        'go_to_page' => [
            'label' => 'Odi na stranica :page',
        ],

        'last' => [
            'label' => 'Posledna',
        ],

        'next' => [
            'label' => 'Sledna',
        ],

        'previous' => [
            'label' => 'Pretchodna',
        ],

    ],

];
