<?php

return [

    'label' => 'Paginació',

    'overview' => '{1} Mostrant 1 resultat|[2,*] Mostrant :first a :last de :total resultats',

    'fields' => [

        'records_per_page' => [

            'label' => 'Per pàgina',

            'options' => [
                'all' => 'All',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Anar a la pàgina :page',
        ],

        'next' => [
            'label' => 'Següent',
        ],

        'previous' => [
            'label' => 'Anterior',
        ],

    ],

];
