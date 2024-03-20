<?php

return [

    'label' => 'Paginació',

    'overview' => '{1} Mostrant 1 resultat|[2,*] Mostrant :first a :last de :total resultats',

    'fields' => [

        'records_per_page' => [

            'label' => 'Per pàgina',

            'options' => [
                'all' => 'Tots',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Primera',
        ],

        'go_to_page' => [
            'label' => 'Anar a la pàgina :page',
        ],

        'last' => [
            'label' => 'Última',
        ],

        'next' => [
            'label' => 'Següent',
        ],

        'previous' => [
            'label' => 'Anterior',
        ],

    ],

];
