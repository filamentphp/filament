<?php

return [

    'label' => 'Pagination navigation',

    'overview' => '{1} Duke shfaqur 1 rezultat|[2,*] Duke shfaqur :first në :last nga :rezultatet totale',

    'fields' => [

        'records_per_page' => [

            'label' => 'Për faqe',

            'options' => [
                'all' => 'Të gjithë',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Shko te faqja :page',
        ],

        'next' => [
            'label' => 'Pasardhësi',
        ],

        'previous' => [
            'label' => 'Paraardhësi',
        ],

    ],

];
