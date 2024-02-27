<?php

return [

    'label' => 'Paginação',

    'overview' => '{1} Exibindo 1 resultado|[2,*] Exibindo :first a :last de :total resultados',

    'fields' => [

        'records_per_page' => [

            'label' => 'por página',

            'options' => [
                'all' => 'Todas',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Primeira',
        ],

        'go_to_page' => [
            'label' => 'Ir para página :page',
        ],

        'last' => [
            'label' => 'Última',
        ],

        'next' => [
            'label' => 'Próxima',
        ],

        'previous' => [
            'label' => 'Anterior',
        ],

    ],

];
