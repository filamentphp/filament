<?php

return [

    'label' => 'Navegación de paginación',

    'overview' => '{1} Se muestra un resultado|[2,*] Se muestran de :first a :last de :total resultados',

    'fields' => [

        'records_per_page' => [

            'label' => 'por página',

            'options' => [
                'all' => 'Todos',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'Primera',
        ],

        'go_to_page' => [
            'label' => 'Ir a la página :page',
        ],

        'last' => [
            'label' => 'Última',
        ],

        'next' => [
            'label' => 'Siguiente',
        ],

        'previous' => [
            'label' => 'Anterior',
        ],

    ],

];
