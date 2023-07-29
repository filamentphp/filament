<?php

return [

    'label' => 'Pagination navigation',

    'overview' => 'Rodomi nuo :first iki :last rezultatai iš :total',

    'fields' => [

        'records_per_page' => [

            'label' => 'per puslapį',

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
