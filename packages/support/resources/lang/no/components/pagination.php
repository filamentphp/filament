<?php

return [

    'label' => 'Sidenavigering',

    'overview' => '{1} Viser 1 resultat|[2,*] Viser :first til :last av :total resultater',

    'fields' => [

        'records_per_page' => [

            'label' => 'Pr. side',

            'options' => [
                'all' => 'Alle',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Første',
        ],

        'go_to_page' => [
            'label' => 'Gå til side :page',
        ],

        'last' => [
            'label' => 'Siste',
        ],

        'next' => [
            'label' => 'Neste',
        ],

        'previous' => [
            'label' => 'Forrige',
        ],

    ],

];
