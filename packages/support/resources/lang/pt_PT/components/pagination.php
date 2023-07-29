<?php

return [

    'label' => 'Paginação',

    'overview' => 'A mostrar :first a :last de :total resultados',

    'fields' => [

        'records_per_page' => [

            'label' => 'por página',

            'options' => [
                'all' => 'Todas',
            ],

        ],

    ],

    'actions' => [

        'go_to_page' => [
            'label' => 'Ir para página :page',
        ],

        'next' => [
            'label' => 'Próximo',
        ],

        'previous' => [
            'label' => 'Anterior',
        ],

    ],

];
