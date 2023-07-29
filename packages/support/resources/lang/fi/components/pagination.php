<?php

return [

    'label' => 'Sivujen navigointi',

    'overview' => '{1} Näytetään 1 tulos|[2,*] Näytetään :first - :last / :total tulosta',

    'fields' => [

        'records_per_page' => [

            'label' => 'per sivu',

            'options' => [
                'all' => 'Kaikki',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Mene sivulle :page',
        ],

        'next' => [
            'label' => 'Seuraava',
        ],

        'previous' => [
            'label' => 'Edellinen',
        ],

    ],

];
