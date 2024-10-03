<?php

return [

    'label' => 'Paginering Navigation',

    'overview' => 'Viser :first til :last af :total resultater',

    'fields' => [

        'records_per_page' => [

            'label' => 'per side',

            'options' => [
                'all' => 'Alle',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Gå til side :page',
        ],

        'next' => [
            'label' => 'Næste',
        ],

        'previous' => [
            'label' => 'Forrige',
        ],

    ],

];
