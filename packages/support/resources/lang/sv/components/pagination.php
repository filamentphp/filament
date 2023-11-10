<?php

return [

    'label' => 'Sidnavigering',

    'overview' => '{1} Visar 1 resultat|[2,*] Visar :first till :last av :total resultat',

    'fields' => [

        'records_per_page' => [

            'label' => 'Per sida',

            'options' => [
                'all' => 'Alla',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Gå till sida :page',
        ],

        'next' => [
            'label' => 'Nästa',
        ],

        'previous' => [
            'label' => 'Föregående',
        ],

    ],

];
